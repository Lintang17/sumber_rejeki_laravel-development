<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('po_detail', function (Blueprint $table) {
            $table->bigInteger('hpp_final')->default(0)->after('hpp_estimasi_gudang');
        });

        Schema::table('po', function (Blueprint $table) {
            $table->dropColumn('hpp_final');
        });
    }

    public function down(): void
    {
        Schema::table('po', function (Blueprint $table) {
            $table->bigInteger('hpp_final')->default(0);
        });

        Schema::table('po_detail', function (Blueprint $table) {
            $table->dropColumn('hpp_final');
        });
    }
};