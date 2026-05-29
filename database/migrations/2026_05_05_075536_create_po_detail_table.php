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
        Schema::create('po_detail', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('po_id');
            $table->string('nama_barang');
            $table->integer('qty');
            $table->bigInteger('harga');
            $table->bigInteger('subtotal');

            $table->text('keterangan')->nullable(); 

            $table->timestamps();

            $table->foreign('po_id')
              ->references('id')
              ->on('po')
              ->onDelete('cascade');
        });
    }
};
