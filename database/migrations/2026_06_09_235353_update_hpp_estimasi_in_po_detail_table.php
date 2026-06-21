<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('po_detail', function (Blueprint $table) {

            $table->renameColumn(
                'hpp_estimasi',
                'hpp_estimasi_admin'
            );

            $table->bigInteger('hpp_estimasi_gudang')
                  ->default(0)
                  ->after('hpp_estimasi_admin');
        });
    }

    public function down()
    {
        Schema::table('po_detail', function (Blueprint $table) {

            $table->dropColumn('hpp_estimasi_gudang');

            $table->renameColumn(
                'hpp_estimasi_admin',
                'hpp_estimasi'
            );
        });
    }
};