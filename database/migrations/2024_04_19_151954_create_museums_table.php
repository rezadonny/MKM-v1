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
        Schema::create('museums', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('alamat')->nullable();
            $table->string('kota')->nullable();
            $table->string('provinsi')->nullable();
            $table->string('kode_pos')->nullable();
            $table->string('telepon')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('medsos_instagram')->nullable();
            $table->string('medsos_twitter')->nullable();
            $table->string('medsos_facebook')->nullable();
            $table->string('medsos_tiktok')->nullable();
            $table->string('googlemap')->nullable();
            $table->string('tipe_koleksi')->nullable();
            $table->string('tipe_pengelola')->nullable();
            $table->string('tipe_area')->nullable();
            $table->string('tipe_audience')->nullable();
            $table->string('tipe_pameran')->nullable();
            $table->string('foto_utama')->nullable();
            $table->string('logo')->nullable();
            $table->longText('keterangan')->nullable();
            $table->string('tanggal_berdiri')->nullable();
            $table->string('pengelola')->nullable();
            $table->string('slug')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('museums');
    }
};
