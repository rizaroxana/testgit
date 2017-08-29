<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProdusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('produs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nume', 255)->index();
            $table->text('descriere');
            $table->integer('buc')->index()->nullable();
            $table->string('status', 255)->index();
            //$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('produs');
    }
}
