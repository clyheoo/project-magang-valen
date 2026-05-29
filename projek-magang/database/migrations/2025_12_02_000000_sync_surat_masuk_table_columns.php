<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SyncSuratMasukTableColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('surat_masuk', function (Blueprint $table) {
            // Drop columns judul_laporan and perihal if they exist
            if (Schema::hasColumn('surat_masuk', 'judul_laporan')) {
                $table->dropColumn('judul_laporan');
            }
            if (Schema::hasColumn('surat_masuk', 'perihal')) {
                $table->dropColumn('perihal');
            }
            
            // Add instruksi_disposisi if not exists
            if (!Schema::hasColumn('surat_masuk', 'instruksi_disposisi')) {
                $table->text('instruksi_disposisi')->nullable()->after('divisi_id');
            }

            // Add instruksi_tambahan if not exists
            if (!Schema::hasColumn('surat_masuk', 'instruksi_tambahan')) {
                $table->text('instruksi_tambahan')->nullable()->after('instruksi_disposisi');
            }

            // Add created_by if not exists
            if (!Schema::hasColumn('surat_masuk', 'created_by')) {
                $table->foreignId('created_by')->nullable()->after('ukuran')->constrained('users')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('surat_masuk', function (Blueprint $table) {
            // Add back columns judul_laporan and perihal
            if (!Schema::hasColumn('surat_masuk', 'judul_laporan')) {
                $table->string('judul_laporan')->after('nomor_surat');
            }
            if (!Schema::hasColumn('surat_masuk', 'perihal')) {
                $table->text('perihal')->after('pengirim');
            }

            // Drop columns instruksi_disposisi and instruksi_tambahan
            if (Schema::hasColumn('surat_masuk', 'instruksi_disposisi')) {
                $table->dropColumn('instruksi_disposisi');
            }
            if (Schema::hasColumn('surat_masuk', 'instruksi_tambahan')) {
                $table->dropColumn('instruksi_tambahan');
            }

            // Drop created_by foreign key and column
            if (Schema::hasColumn('surat_masuk', 'created_by')) {
                $table->dropForeign(['created_by']);
                $table->dropColumn('created_by');
            }
        });
    }
}
