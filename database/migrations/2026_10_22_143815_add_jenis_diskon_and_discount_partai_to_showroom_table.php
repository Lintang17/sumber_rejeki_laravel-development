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
        Schema::table('showroom', function (Blueprint $table) {
            $table->string('jenis_diskon', 20)
                ->default('non_partai')
                ->after('discount');

            $table->unsignedTinyInteger('discount_partai')
                ->nullable()
                ->after('jenis_diskon');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('showroom', function (Blueprint $table) {
            $table->dropColumn([
                'jenis_diskon',
                'discount_partai',
            ]);
        });
    }
};
