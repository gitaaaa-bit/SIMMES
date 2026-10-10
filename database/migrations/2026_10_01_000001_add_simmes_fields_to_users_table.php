<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Tabel users sudah ada (dari auth kamu). Kolom hanya ditambah bila belum ada.
        Schema::table('users', function (Blueprint $t) {
            if (!Schema::hasColumn('users', 'nik'))        $t->string('nik')->nullable()->unique();
            if (!Schema::hasColumn('users', 'role'))       $t->string('role')->default('teknisi');   // manajemen | teknisi
            if (!Schema::hasColumn('users', 'status'))     $t->string('status')->default('aktif');   // pending | aktif | ditolak
            if (!Schema::hasColumn('users', 'department')) $t->string('department')->nullable();
            if (!Schema::hasColumn('users', 'bagian'))     $t->string('bagian')->nullable();
            if (!Schema::hasColumn('users', 'whatsapp'))   $t->string('whatsapp')->nullable();
        });
    }

    public function down(): void {}
};
