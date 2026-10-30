
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('history_hapus', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('nama_user')->nullable();

            $table->string('jenis_data', 100);
            $table->unsignedBigInteger('data_id')->nullable();
            $table->string('nama_data')->nullable();

            // Salinan data untuk membantu proses pemulihan
            $table->json('data_snapshot')->nullable();

            $table->timestamp('dihapus_pada')->useCurrent();
            $table->timestamp('dipulihkan_pada')->nullable();
            $table->timestamp('dihapus_permanen_pada')->nullable();

            $table->timestamps();

            $table->index(['jenis_data', 'data_id']);
            $table->index('dihapus_pada');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('history_hapus');
    }
};
