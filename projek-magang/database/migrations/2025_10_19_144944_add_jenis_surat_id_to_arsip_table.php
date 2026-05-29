<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('arsip', function (Blueprint $table) {
            // Tambahkan kolom jenis_surat_id jika belum ada
            if (!Schema::hasColumn('arsip', 'jenis_surat_id')) {
                $table->unsignedBigInteger('jenis_surat_id')->nullable()->after('ukuran');
                $table->foreign('jenis_surat_id')
                      ->references('id')
                      ->on('jenis_dokumen')
                      ->onDelete('cascade');
            }

            // Tambahkan kolom format_file_id jika belum ada
            if (!Schema::hasColumn('arsip', 'format_file_id')) {
                $table->unsignedBigInteger('format_file_id')->nullable()->after('jenis_surat_id');
                $table->foreign('format_file_id')
                      ->references('id')
                      ->on('format_file')
                      ->onDelete('cascade');
            }
        });
    }

    /**
     * Batalkan migrasi.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('arsip', function (Blueprint $table) {
            // Hapus foreign key & kolom jenis_surat_id jika ada
            if (Schema::hasColumn('arsip', 'jenis_surat_id')) {
                $table->dropForeign(['jenis_surat_id']);
                $table->dropColumn('jenis_surat_id');
            }

            // Hapus foreign key & kolom format_file_id jika ada
            if (Schema::hasColumn('arsip', 'format_file_id')) {
                $table->dropForeign(['format_file_id']);
                $table->dropColumn('format_file_id');
            }
        });
    }
};
