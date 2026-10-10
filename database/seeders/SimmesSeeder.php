<?php

namespace Database\Seeders;

use App\Models\{Machine, MaintenanceSchedule, Ticket, User};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/** php artisan db:seed --class=SimmesSeeder   (data contoh dari Figma) */
class SimmesSeeder extends Seeder
{
    public function run(): void
    {
        $mk = fn ($nik, $name, $role, $dept, $wa = null) => User::updateOrCreate(['nik' => $nik], [
            'name' => $name, 'email' => "$nik@simmes.local", 'password' => Hash::make('password'),
            'role' => $role, 'status' => 'aktif', 'department' => $dept, 'whatsapp' => $wa,
        ]);
        $mgr = $mk('100001', 'Rina Manajemen', 'manajemen', 'HRD');
        $t1  = $mk('200001', 'Budi Santoso', 'teknisi', 'Teknik', '081200000001');
        $t2  = $mk('200002', 'Agus Pratama', 'teknisi', 'Teknik', '081200000002');
        User::updateOrCreate(['nik' => '300001'], ['name' => 'Calon Karyawan 1', 'email' => '300001@simmes.local',
            'password' => Hash::make('password'), 'role' => 'teknisi', 'status' => 'pending', 'department' => 'Produksi A']);
        User::updateOrCreate(['nik' => '300002'], ['name' => 'Calon Karyawan 2', 'email' => '300002@simmes.local',
            'password' => Hash::make('password'), 'role' => 'teknisi', 'status' => 'pending', 'department' => 'Gudang']);

        $m = fn ($c, $n, $cat, $loc, $int = 3, $st = 'aktif') => Machine::updateOrCreate(['code' => $c], [
            'name' => $n, 'category' => $cat, 'location' => $loc, 'maintenance_interval_months' => $int,
            'status' => $st, 'model' => 'Seri X', 'serial_number' => 'SN-'.$c, 'purchase_date' => now()->subYears(2),
        ]);
        $press = $m('MSN-014', 'Mesin Press 2', 'Mesin produksi', 'Produksi A', 3, 'diproses');
        $pack  = $m('MSN-022', 'Mesin Packing', 'Mesin produksi', 'Gudang', 3, 'dilaporkan');
        $mixer = $m('MSN-005', 'Mixer', 'Mesin produksi', 'Produksi B');
        $kopi  = $m('MSN-031', 'Mesin Kopi', 'Peralatan', 'Kantin', 1, 'dilaporkan');
        $ac3   = $m('AC-003', 'AC Ruang Kontrol', 'AC', 'Produksi A', 3);
        $mtr   = $m('MTR-001', 'Motor Utama', 'Mesin produksi', 'Produksi B', 6);

        $tk = function ($mc, $tech, $urg, $st, $comp, $dueDays, $ago) {
            $t = Ticket::create([
                'code' => Ticket::nextCode(), 'machine_id' => $mc->id, 'reporter_id' => 1,
                'technician_id' => $tech?->id, 'urgency' => $urg, 'status' => $st, 'complaint' => $comp,
                'due_at' => now()->addDays($dueDays), 'created_at' => now()->subDays($ago),
                'progress' => ['dilaporkan' => 0, 'ditugaskan' => 0, 'diproses' => 50, 'selesai' => 100][$st],
                'finished_at' => $st === 'selesai' ? now()->subDays(1) : null,
            ]);
            $t->log('Dilaporkan', 'Operator');
            if ($tech) $t->log('Ditugaskan', $tech->name);
            return $t;
        };
        if (!Ticket::count()) {
            $tk($press, $t1, 'tinggi', 'diproses', 'Mesin berhenti sendiri dan mengeluarkan bunyi keras.', 1, 1);
            $tk($pack, null, 'sedang', 'dilaporkan', 'Sensor tidak terbaca.', 3, 2);
            $tk($mixer, $t2, 'rendah', 'selesai', 'Pembersihan rutin.', 2, 5);
            $tk($kopi, $t1, 'tinggi', 'ditugaskan', 'Kerak di tangki, hasil seduhan pahit.', -2, 6);
        }

        $s = fn ($mc, $task, $type, $int, $days, $tech = null, $st = 'terjadwal', $p = 0) => MaintenanceSchedule::firstOrCreate(
            ['machine_id' => $mc->id, 'task' => $task],
            ['type' => $type, 'interval_months' => $int, 'interval_label' => $int ? "$int bulan" : null,
             'due_date' => now()->addDays($days), 'technician_id' => $tech?->id, 'status' => $st, 'progress' => $p,
             'completed_at' => $st === 'selesai' ? now()->subDays(2) : null]);
        $s($kopi, 'Pembersihan rutin', 'rutin', 1, -3, $t1);
        $s($press, 'Pelumasan', 'rutin', 1, 4, $t1, 'dikerjakan', 50);
        $s($pack, 'Ganti sensor', 'rutin', 3, 20);
        $s($mixer, 'Ganti belt', 'rutin', 6, -2, $t2, 'selesai', 100);
        $s($ac3, 'Cek mesin AC', 'rutin', 3, 6, $t2);
        $s($mtr, 'Ganti oli', 'rutin', 6, 12, $t1);
    }
}
