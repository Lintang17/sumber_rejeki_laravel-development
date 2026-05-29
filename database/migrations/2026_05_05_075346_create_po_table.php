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
        Schema::create('po', function (Blueprint $table) {
            $table->id();
            $table->string('kode_po')->unique();
            $table->date('tanggal');
            $table->string('supplier');
            $table->bigInteger('total')->default(0);

            $table->enum('status', [
                'Pending',
                'Disetujui',
                'Diproses',
                'Dikirim',
                'Selesai'
            ])->default('Pending');

            $table->timestamp('dikirim_at')->nullable();
            $table->timestamp('diterima_at')->nullable();

            $table->timestamps();
        });
    }
};
