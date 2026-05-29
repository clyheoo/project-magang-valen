<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArsipTable extends Migration
{
    public function up()
    {
        Schema::create('arsip', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_surat');
            $table->date('tanggal');
            $table->string('perihal');
            $table->string('status')->default('baru');
            $table->string('jenis');
            $table->unsignedBigInteger('format_file_id');
            $table->string('file_path')->nullable();
            $table->string('ukuran')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            $table->foreign('format_file_id')->references('id')->on('format_file')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('arsip');
    }
}
