<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTrackingSourceToTracks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tracks', function($table) {
            DB::statement('SET SQL_MODE = "ALLOW_INVALID_DATES";');
            DB::statement('ALTER TABLE tracks ALTER COLUMN `point_time` DROP DEFAULT');
            DB::statement('ALTER TABLE tracks CHANGE COLUMN `point_time` `point_time` DATETIME NOT NULL');
            $table->string('tracks_source', 16)->nullable(true)->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tracks', function($table) {
            $table->dropColumn('tracks_source');
            DB::statement('ALTER TABLE tracks CHANGE COLUMN `point_time` `point_time` TIMESTAMP NOT NULL');
        });
    }
}
