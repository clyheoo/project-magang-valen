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
        Schema::table('surat_keluar', function (Blueprint $table) {
            $table->unsignedBigInteger('divisi_id')->nullable()->after('perihal');
            $table->string('status', 50)->default('draft')->after('divisi_id');
            // Rename tujuan to penerima using raw statement for MariaDB compatibility
            \DB::statement('ALTER TABLE surat_keluar CHANGE tujuan penerima VARCHAR(255);');
            // Foreign key for divisi_id if not exists (idempotent)
            $table->foreign('divisi_id')->references('id')->on('divisi')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('surat_keluar', function (Blueprint $table) {
            $table->dropForeign(['divisi_id']);
            $table->dropColumn(['divisi_id', 'status']);
            // Reverse rename using raw statement
            \DB::statement('ALTER TABLE surat_keluar CHANGE penerima tujuan VARCHAR(255);');
        });
    }
};
