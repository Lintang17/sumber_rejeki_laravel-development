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
            $table->string('no_hp')->nullable()->after('customer');
            $table->text('alamat')->nullable()->after('no_hp');
            $table->bigInteger('hpp_final')->default(0)->after('total');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('po', function (Blueprint $table) {
            $table->dropColumn([
                'no_hp',
                'alamat',
                'hpp_final'
            ]);
        });
    }
};