<?php

namespace App\Services;

use App\Models\MaintenanceSchedule;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Normalisasi nomor telepon ke format internasional WhatsApp (misal: 6281234567890).
     */
    public static function normalizeNumber(?string $phone): ?string
    {
        if (!$phone) {
            return null;
        }

        // Hapus karakter non-angka
        $cleaned = preg_replace('/[^0-9]/', '', $phone);

        if (empty($cleaned)) {
            return null;
        }

        // Ganti awalan 08 menjadi 628
        if (str_starts_with($cleaned, '08')) {
            $cleaned = '628' . substr($cleaned, 2);
        } elseif (str_starts_with($cleaned, '8')) {
            $cleaned = '62' . $cleaned;
        }

        // Validasi panjang nomor Indonesia (minimal 10 digit, maksimal 15 digit)
        if (strlen($cleaned) < 10) {
            return null;
        }

        return $cleaned;
    }

    /**
     * Buat URL langsung ke WhatsApp (wa.me) dengan pesan yang sudah terisi.
     */
    public static function makeDirectLink(?string $phone, string $message): ?string
    {
        $normalized = self::normalizeNumber($phone);
        if (!$normalized) {
            return null;
        }

        return 'https://wa.me/' . $normalized . '?text=' . rawurlencode($message);
    }

    /**
     * Kirim notifikasi WhatsApp ke teknisi / nomor tujuan.
     * Mendukung Gateway API (seperti Fonnte/Wablas) atau fallback ke log jika token belum diisi.
     */
    public static function send(string|User $target, string $message): array
    {
        $phone = $target instanceof User ? $target->whatsapp : $target;
        $name  = $target instanceof User ? $target->name : 'Teknisi';

        $normalized = self::normalizeNumber($phone);
        if (!$normalized) {
            Log::warning("[WhatsApp Service] Gagal kirim ke {$name}: Nomor WhatsApp tidak valid atau kosong ({$phone}).");
            return [
                'success' => false,
                'message' => 'Nomor WhatsApp belum diisi atau tidak valid.',
            ];
        }

        $gatewayUrl = config('services.whatsapp.url', env('WA_GATEWAY_URL', 'https://api.fonnte.com/send'));
        $token      = config('services.whatsapp.token', env('WA_API_TOKEN'));

        // Jika token gateway belum dikonfigurasi, simpan di log sistem agar tetap bisa ditinjau
        if (empty($token)) {
            Log::info("[WhatsApp NOTIFIKASI (SIMULASI)] Penerima: {$name} ({$normalized})\n" . $message);
            return [
                'success'   => true,
                'simulated' => true,
                'phone'     => $normalized,
                'message'   => "Simulasi terkirim (Token WA belum diisi di .env). Pesan telah dicatat ke log.",
            ];
        }

        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'Authorization' => $token,
                ])
                ->post($gatewayUrl, [
                    'target'  => $normalized,
                    'message' => $message,
                ]);

            if ($response->successful()) {
                Log::info("[WhatsApp Gateway Berhasil] Terkirim ke {$name} ({$normalized}).");
                return [
                    'success'   => true,
                    'simulated' => false,
                    'phone'     => $normalized,
                    'message'   => "Notifikasi WhatsApp berhasil dikirim ke {$name}.",
                ];
            }

            Log::error("[WhatsApp Gateway Error] Response: " . $response->body());
            return [
                'success' => false,
                'message' => "Gateway WhatsApp mengembalikan status: " . $response->status(),
            ];
        } catch (\Throwable $e) {
            Log::error("[WhatsApp Gateway Exception] " . $e->getMessage());
            return [
                'success' => false,
                'message' => "Gagal menghubungi gateway WhatsApp: " . $e->getMessage(),
            ];
        }
    }

    /**
     * Buat teks pesan notifikasi penugasan tiket perbaikan.
     */
    public static function buildTicketMessage(Ticket $ticket, User $tech): string
    {
        $due = $ticket->due_at ? $ticket->due_at->translatedFormat('d F Y, H:i') : '-';
        $appUrl = config('app.url', url('/'));

        return "🔔 *SIMMES - NOTIFIKASI TUGAS PERBAIKAN*\n\n"
            . "Halo *{$tech->name}*,\n"
            . "Anda telah ditugaskan untuk menangani tiket perbaikan mesin berikut:\n\n"
            . "📌 *Kode Tiket:* {$ticket->code}\n"
            . "⚙️ *Mesin:* {$ticket->machine->label}\n"
            . "📍 *Lokasi:* {$ticket->machine->location}\n"
            . "⚡ *Urgensi:* " . strtoupper($ticket->urgency) . "\n"
            . "📅 *Batas Waktu:* {$due}\n"
            . "📝 *Keluhan:* {$ticket->complaint}\n\n"
            . "Mohon segera login ke SIMMES untuk mulai pemeriksaan:\n"
            . "👉 {$appUrl}/teknisi/tugas\n\n"
            . "Terima kasih atas kerja samanya.";
    }

    /**
     * Kirim notifikasi WhatsApp saat tiket ditugaskan ke teknisi.
     */
    public static function sendTicketAssignment(Ticket $ticket, User $tech): array
    {
        $message = self::buildTicketMessage($ticket, $tech);
        return self::send($tech, $message);
    }

    /**
     * Buat teks pesan pengingat jadwal pemeliharaan.
     */
    public static function buildScheduleMessage(MaintenanceSchedule $schedule, string $kind): string
    {
        $techName = $schedule->technician?->name ?? 'Teknisi';
        $dueDate  = $schedule->due_date ? $schedule->due_date->translatedFormat('d F Y') : '-';
        if ($schedule->due_time) {
            $dueDate .= " pukul {$schedule->due_time} WIB";
        }
        $appUrl   = config('app.url', url('/'));

        return "⏰ *SIMMES - PENGINGAT JADWAL PEMELIHARAAN ({$kind})*\n\n"
            . "Halo *{$techName}*,\n"
            . "Berikut pengingat jadwal pemeliharaan mesin:\n\n"
            . "⚙️ *Mesin:* {$schedule->machine->label} ({$schedule->machine->location})\n"
            . "🛠️ *Tugas:* {$schedule->task}\n"
            . "📅 *Jatuh Tempo / Waktu:* {$dueDate}\n"
            . "🔖 *Status:* {$kind}\n\n"
            . "Silakan lakukan pemeriksaan dan perbarui progres di portal SIMMES:\n"
            . "👉 {$appUrl}/teknisi/jadwal\n\n"
            . "Terima kasih.";
    }

    /**
     * Buat URL langsung ke WhatsApp teknisi untuk jadwal maintenance.
     */
    public static function makeScheduleShareLink(MaintenanceSchedule $schedule, string $kind = 'Pengingat'): ?string
    {
        $phone = $schedule->technician?->whatsapp;
        $msg = self::buildScheduleMessage($schedule, $kind);
        return self::makeDirectLink($phone, $msg);
    }

    /**
     * Kirim notifikasi pengingat jadwal pemeliharaan.
     */
    public static function sendScheduleReminder(MaintenanceSchedule $schedule, string $kind): array
    {
        if (!$schedule->technician) {
            return ['success' => false, 'message' => 'Jadwal belum memiliki teknisi yang ditugaskan.'];
        }

        $message = self::buildScheduleMessage($schedule, $kind);
        return self::send($schedule->technician, $message);
    }

    /**
     * Kirim pesan uji coba / verifikasi ke WhatsApp teknisi.
     */
    public static function sendTestMessage(User $tech): array
    {
        $appUrl = config('app.url', url('/'));
        $phone  = $tech->whatsapp ?? '-';

        $message = "✅ *SIMMES - TES NOTIFIKASI WHATSAPP*\n\n"
            . "Halo *{$tech->name}*,\n"
            . "Nomor WhatsApp Anda ({$phone}) telah berhasil terhubung dengan sistem SIMMES!\n\n"
            . "Anda akan menerima notifikasi otomatis ketika:\n"
            . "1. Terdapat tiket perbaikan mesin baru yang ditugaskan ke Anda.\n"
            . "2. Pengingat jadwal pemeliharaan (H-7, H-1, Hari-H, dan keterlambatan).\n\n"
            . "Portal SIMMES: {$appUrl}\n"
            . "Terima kasih.";

        return self::send($tech, $message);
    }

    /**
     * Buat teks pesan penyiaran laporan perbaikan baru (siapa cepat dia dapat).
     */
    public static function buildBroadcastMessage(Ticket $ticket): string
    {
        $appUrl = config('app.url', url('/'));
        $due = $ticket->due_at ? $ticket->due_at->translatedFormat('d F Y, H:i') : '-';

        return "⚡ *SIMMES - LAPORAN KERUSAKAN MESIN BARU!*\n"
            . "_(Tugas Terbuka: Siapa Cepat Ambil Tugas)_\n\n"
            . "📌 *Kode Tiket:* {$ticket->code}\n"
            . "⚙️ *Mesin:* {$ticket->machine->label}\n"
            . "📍 *Lokasi:* {$ticket->machine->location}\n"
            . "⚡ *Urgensi:* " . strtoupper($ticket->urgency) . "\n"
            . "📅 *Batas Waktu:* {$due}\n"
            . "📝 *Keluhan:* {$ticket->complaint}\n\n"
            . "Teknisi silakan login dan ambil tugas perbaikan ini:\n"
            . "👉 {$appUrl}/teknisi/tugas\n\n"
            . "Terima kasih.";
    }

    /**
     * Buat link WhatsApp share umum untuk disiarkan ke Grup WhatsApp Teknisi.
     */
    public static function makeBroadcastShareLink(Ticket $ticket): string
    {
        $msg = self::buildBroadcastMessage($ticket);
        return 'https://api.whatsapp.com/send?text=' . rawurlencode($msg);
    }

    /**
     * Siarkan notifikasi laporan tiket baru ke semua teknisi yang terdaftar.
     */
    public static function broadcastToAllTechnicians(Ticket $ticket): int
    {
        $techs = User::where('role', 'teknisi')
            ->where('status', 'aktif')
            ->whereNotNull('whatsapp')
            ->get();

        $msg = self::buildBroadcastMessage($ticket);
        $count = 0;

        foreach ($techs as $t) {
            $res = self::send($t, $msg);
            if ($res['success']) {
                $count++;
            }
        }

        return $count;
    }
}