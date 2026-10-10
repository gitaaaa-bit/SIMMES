<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nik', 30)->nullable()->unique()->after('name');
            $table->string('role', 20)->default('teknisi')->after('nik');
            $table->string('status', 20)->default('pending')->after('role');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->string('email')->nullable()->change(); // login memakai NIK, email tidak wajib
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('approved_by');
            $table->dropColumn(['nik', 'role', 'status', 'approved_at']);
        });
    }
};
