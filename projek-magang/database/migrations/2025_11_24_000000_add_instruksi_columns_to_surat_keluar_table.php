<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInstruksiColumnsToSuratKeluarTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('surat_keluar', function (Blueprint $table) {
            if (!Schema::hasColumn('surat_keluar', 'instruksi_disposisi')) {
                $table->text('instruksi_disposisi')->nullable()->after('nomor_surat');
            }
            if (!Schema::hasColumn('surat_keluar', 'instruksi_tambahan')) {
                $table->text('instruksi_tambahan')->nullable()->after('instruksi_disposisi');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_keluar', function (Blueprint $table) {
            if (Schema::hasColumn('surat_keluar', 'instruksi_disposisi')) {
                $table->dropColumn('instruksi_disposisi');
            }
            if (Schema::hasColumn('surat_keluar', 'instruksi_tambahan')) {
                $table->dropColumn('instruksi_tambahan');
            }
        });
    }
}
