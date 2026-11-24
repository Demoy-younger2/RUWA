<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'rfid_uid')) {
                $table->string('rfid_uid', 50)->nullable()->unique()->after('nik');
                $table->timestamp('rfid_registered_at')->nullable()->after('rfid_uid');
            }
        });

        Schema::table('wargas', function (Blueprint $table) {
            if (!Schema::hasColumn('wargas', 'rfid_uid')) {
                $table->string('rfid_uid', 50)->nullable()->after('nik');
            }
        });

        if (!Schema::hasTable('rfid_scans')) {
            Schema::create('rfid_scans', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('rfid_uid', 50);
                $table->string('device_id', 50)->default('ESP32-RFID');
                $table->string('status', 30)->default('success'); // registered, recognized, unregistered, error
                $table->string('action', 50)->default('scan'); // scan, register, tap_access
                $table->text('message')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rfid_scans');

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'rfid_uid')) {
                $table->dropUnique(['rfid_uid']);
                $table->dropColumn(['rfid_uid', 'rfid_registered_at']);
            }
        });

        Schema::table('wargas', function (Blueprint $table) {
            if (Schema::hasColumn('wargas', 'rfid_uid')) {
                $table->dropColumn('rfid_uid');
            }
        });
    }
};
