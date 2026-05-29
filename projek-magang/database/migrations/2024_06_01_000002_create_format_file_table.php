<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFormatFileTable extends Migration
{
    public function up()
    {
        Schema::create('format_file', function (Blueprint $table) {
            $table->id();
            $table->string('nama_format');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('format_file');
    }
}
