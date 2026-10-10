<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('machines', function (Blueprint $t) {
            $t->id();
            $t->string('code')->unique();           // MSN-014
            $t->string('name');                     // Mesin Press 2
            $t->string('category')->nullable();     // Mesin produksi
            $t->string('model')->nullable();
            $t->string('serial_number')->nullable();
            $t->date('purchase_date')->nullable();
            $t->string('location');
            $t->string('condition')->nullable();
            $t->unsignedSmallInteger('maintenance_interval_months')->default(3);
            $t->string('status')->default('aktif'); // aktif | dilaporkan | diproses | nonaktif
            $t->date('last_service_at')->nullable();
            $t->json('media')->nullable();          // dokumentasi awal (foto/video)
            $t->timestamps();
        });

        Schema::create('tickets', function (Blueprint $t) {
            $t->id();
            $t->string('code')->unique();           // TK-0101
            $t->foreignId('machine_id')->constrained()->cascadeOnDelete();
            $t->foreignId('reporter_id')->nullable()->constrained('users')->nullOnDelete();
            $t->foreignId('technician_id')->nullable()->constrained('users')->nullOnDelete();
            $t->string('type')->default('insiden'); // insiden | rutin
            $t->text('complaint');
            $t->string('urgency')->default('sedang');   // rendah | sedang | tinggi
            $t->string('status')->default('dilaporkan'); // dilaporkan | ditugaskan | diproses | selesai
            $t->unsignedTinyInteger('progress')->default(0); // 0/25/50/75/100
            $t->string('action')->nullable();       // tindakan: Ganti bearing
            $t->string('spareparts')->nullable();
            $t->dateTime('due_at')->nullable();
            $t->dateTime('started_at')->nullable();
            $t->dateTime('finished_at')->nullable();
            $t->timestamps();
        });

        Schema::create('ticket_media', function (Blueprint $t) {
            $t->id();
            $t->foreignId('ticket_id')->constrained()->cascadeOnDelete();
            $t->string('phase');                    // report | before | after
            $t->string('kind')->default('photo');   // photo | video
            $t->string('path');
            $t->timestamps();
        });

        Schema::create('ticket_logs', function (Blueprint $t) {
            $t->id();
            $t->foreignId('ticket_id')->constrained()->cascadeOnDelete();
            $t->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $t->string('label');                    // Dilaporkan, Ditugaskan, Diproses ...
            $t->text('note')->nullable();
            $t->timestamps();
        });

        Schema::create('maintenance_schedules', function (Blueprint $t) {
            $t->id();
            $t->foreignId('machine_id')->constrained()->cascadeOnDelete();
            $t->foreignId('technician_id')->nullable()->constrained('users')->nullOnDelete();
            $t->foreignId('ticket_id')->nullable()->constrained()->nullOnDelete();
            $t->string('type')->default('rutin');   // rutin | insiden
            $t->string('task');                     // Cek AC, Ganti oli
            $t->string('interval_label')->nullable(); // 3 bulan
            $t->unsignedSmallInteger('interval_months')->nullable();
            $t->date('due_date');
            $t->unsignedTinyInteger('progress')->default(0);
            $t->string('status')->default('terjadwal'); // terjadwal | dikerjakan | selesai
            $t->string('spareparts')->nullable();
            $t->string('before_path')->nullable();
            $t->string('after_path')->nullable();
            $t->date('completed_at')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_schedules');
        Schema::dropIfExists('ticket_logs');
        Schema::dropIfExists('ticket_media');
        Schema::dropIfExists('tickets');
        Schema::dropIfExists('machines');
    }
};
