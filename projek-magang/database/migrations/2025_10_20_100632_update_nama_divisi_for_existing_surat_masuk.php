<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Update nama_divisi untuk data existing berdasarkan divisi_id
        DB::statement("
            UPDATE surat_masuk
            SET nama_divisi = (
                SELECT d.nama_divisi
                FROM divisi d
                WHERE d.id = surat_masuk.divisi_id
            )
            WHERE divisi_id IS NOT NULL AND (nama_divisi IS NULL OR nama_divisi = '')
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Kosongkan nama_divisi untuk rollback
        DB::statement("UPDATE surat_masuk SET nama_divisi = NULL WHERE divisi_id IS NOT NULL");
    }
};
