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
        Schema::table('billingoptions', function($table) {
			$table->integer('requires_comment')->nullable(true)->default(0);
			$table->integer('other_club')->nullable(true)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('billingoptions', function($table) {
            $table->dropColumn('requires_comment');
			$table->dropColumn('other_club');
        });
    }
}
