<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFotoTokoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('foto_toko', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_toko');
            $table->text('url');
            $table->timestamps();

            $table->foreign('id_toko')->references('id')->on('toko')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('foto_toko');
    }
}
