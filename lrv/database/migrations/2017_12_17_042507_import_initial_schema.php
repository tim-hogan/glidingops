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
        $gliding_schema = realpath(__DIR__.'/../gliding-initial-schema.sql');
        DB::unprepared( file_get_contents($gliding_schema) );

        // $tracks_schema = realpath(__DIR__.'/../tracks-initial-schema.sql');
        // DB::unprepared( file_get_contents($tracks_schema) );
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
