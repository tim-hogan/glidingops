<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ImportInitialSchema extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // will import tables from the original mysql dump file
        $file = realpath(__DIR__.'/../initial-schema.sql');
        DB::unprepared( file_get_contents($file) );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        throw new RuntimeException("There is no way back from this point on!");
    }
}
