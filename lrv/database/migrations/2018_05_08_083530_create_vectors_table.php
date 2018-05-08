<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVectorsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('vectors', function (Blueprint $table) {
      $table->increments('id');
      $table->timestamps();

      $table->integer('organisation_id');
      $table->string('designation');
      $table->string('location');

      $table->foreign('organisation_id')->references('id')->on('organisations');
      $table->unique(['designation', 'location']);
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('vectors');
  }
}
