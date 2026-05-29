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
        Schema::table('riwayat_status', function (Blueprint $table) {
            $table->unsignedBigInteger('surat_masuk_id')->nullable()->after('arsip_id');
            $table->foreign('surat_masuk_id')->references('id')->on('surat_masuk')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('riwayat_status', function (Blueprint $table) {
            $table->dropForeign(['surat_masuk_id']);
            $table->dropColumn('surat_masuk_id');
        });
    }
};
