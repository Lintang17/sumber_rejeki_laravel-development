<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('po', function (Blueprint $table) {

            $table->bigInteger('dp')
                    ->default(0)
                    ->after('total');

            $table->bigInteger('sisa_pembayaran')
                    ->default(0)
                    ->after('dp');

            $table->string('metode_pembayaran')
                    ->nullable()
                    ->after('sisa_pembayaran');
        });
    }

    public function down(): void
    {
        Schema::table('po', function (Blueprint $table) {

            $table->dropColumn([
                'dp',
                'sisa_pembayaran',
                'metode_pembayaran'
            ]);
        });
    }
};