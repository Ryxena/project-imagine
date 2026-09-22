<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('last_read_verifikasi')->nullable();
            $table->timestamp('last_read_keluhan_admin')->nullable();
            $table->timestamp('last_read_tagihan_penghuni')->nullable();
            $table->timestamp('last_read_keluhan_penghuni')->nullable();
            $table->timestamp('last_read_pengumuman_penghuni')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
