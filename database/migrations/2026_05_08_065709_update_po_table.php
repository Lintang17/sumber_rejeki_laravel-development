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
        Schema::table('po', function (Blueprint $table) {

            $table->string('produk')->nullable()->after('supplier');

            $table->text('deskripsi')
                  ->nullable()
                  ->after('produk');

            $table->integer('qty')
                  ->default(0)
                  ->after('deskripsi');

            $table->bigInteger('hpp_estimasi')
                  ->default(0)
                  ->after('qty');

            $table->bigInteger('harga_jual')
                  ->default(0)
                  ->after('hpp_estimasi');

            $table->date('estimasi_awal')
                  ->nullable()
                  ->after('status');

            $table->date('estimasi_akhir')
                  ->nullable()
                  ->after('estimasi_awal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('po', function (Blueprint $table) {

            $table->dropColumn([
                'produk',
                'deskripsi',
                'qty',
                'hpp_estimasi',
                'harga_jual',
                'estimasi_awal',
                'estimasi_akhir'
            ]);
        });
    }
};