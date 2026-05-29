<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRiwayatStatusTable extends Migration
{
    public function up()
    {
        Schema::create('riwayat_status', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('arsip_id');
            $table->string('status');
            $table->unsignedBigInteger('user_id');
            $table->text('keterangan')->nullable();
            $table->timestamp('tanggal')->useCurrent();
            $table->timestamps();

            $table->foreign('arsip_id')->references('id')->on('arsip')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('riwayat_status');
    }
}
