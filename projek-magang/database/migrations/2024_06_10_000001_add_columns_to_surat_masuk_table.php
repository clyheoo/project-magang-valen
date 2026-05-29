<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToSuratMasukTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('surat_masuk', function (Blueprint $table) {
            $table->string('judul_laporan', 255)->after('nomor_surat');
            $table->foreignId('format_file_id')->after('pengirim')->constrained('format_file');
            $table->integer('ukuran')->nullable()->after('file_path');
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
            $table->dropForeign(['format_file_id']);
            $table->dropColumn(['judul_laporan', 'format_file_id', 'ukuran']);
        });
    }
}
