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
        Schema::create('deliveries', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('id_kendaraan');
            $table->string('id_sopir');
            $table->string('kondisi');
            $table->integer('berat_barang');
            $table->string('jenis_barang');
            $table->date('date');
            $table->integer('jml_barang');
            $table->string('id_tujuan');
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};
