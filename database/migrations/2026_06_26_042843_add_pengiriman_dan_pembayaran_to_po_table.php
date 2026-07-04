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

            // Status pembayaran
            $table->enum('status_pembayaran', [
                'DP',
                'Lunas'
            ])->default('DP')->after('sisa_pembayaran');

            // Tanggal pengiriman
            $table->dateTime('tanggal_dikirim')
                ->nullable()
                ->after('status');

            // Tanggal selesai
            $table->dateTime('tanggal_selesai')
                ->nullable()
                ->after('tanggal_dikirim');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('po', function (Blueprint $table) {

            $table->dropColumn([
                'status_pembayaran',
                'tanggal_dikirim',
                'tanggal_selesai'
            ]);

        });
    }
};
