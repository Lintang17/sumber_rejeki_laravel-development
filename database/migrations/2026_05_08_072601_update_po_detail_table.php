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
        Schema::table('po_detail', function (Blueprint $table) {

            $table->renameColumn('nama_barang', 'produk');
            $table->renameColumn('harga', 'hpp_estimasi');
            $table->renameColumn('keterangan', 'deskripsi');

            $table->bigInteger('harga_jual')
                  ->default(0)
                  ->after('hpp_estimasi');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('po_detail', function (Blueprint $table) {

            $table->renameColumn('produk', 'nama_barang');
            $table->renameColumn('hpp_estimasi', 'harga');
            $table->renameColumn('deskripsi', 'keterangan');

            $table->dropColumn('harga_jual');

        });
    }
};