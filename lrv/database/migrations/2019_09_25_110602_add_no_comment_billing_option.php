<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNoCommentBillingOption extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tracks', function($table) {
            DB::statement('ALTER TABLE `billingoptions` ADD `no_comment` int(11) DEFAULT 0;');
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
            DB::statement('ALTER TABLE billingoptions DROP COLUMN `no_comment`');
        });
    }
}
