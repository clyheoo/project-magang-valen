<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('divisi', function (Blueprint $table) {
            $table->integer('surat_masuk_count')->default(0)->after('deskripsi');
            $table->integer('surat_keluar_count')->default(0)->after('surat_masuk_count');
            $table->integer('belum_ditindak_count')->default(0)->after('surat_keluar_count');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('divisi', function (Blueprint $table) {
            $table->dropColumn(['surat_masuk_count', 'surat_keluar_count', 'belum_ditindak_count']);
        });
    }
};
