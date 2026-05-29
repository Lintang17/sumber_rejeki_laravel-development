<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeletedAtToProdukTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('produk', 'deleted_at')) {

            Schema::table('produk', function (Blueprint $table) {
                $table->softDeletes();
            });

        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('produk', 'deleted_at')) {

            Schema::table('produk', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });

        }
    }
};