<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddParticleTrackingToAircraft extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('aircraft', function($table) {
            $table->string('aircraft_particle_id', 24)->nullable()->default(null);
            $table->string('aircraft_track_udp_server', 16)->nullable()->default(null);
            $table->integer('aircraft_track_timer_1')->nullable()->default(null);
            $table->integer('aircraft_track_timer_2')->nullable()->default(null);
            $table->integer('aircraft_track_timer_3')->nullable()->default(null);
            $table->integer('aircraft_track_timer_4')->nullable()->default(null);
            $table->boolean('aircraft_track_debug')->nullable()->default(null);
            $table->enum('aircraft_track_last_status', array('nofix','pos','hello'))->nullable()->default(null);
            $table->dateTime('aircraft_track_status_timestamp')->nullable()->default(null);
            $table->float('aircraft_track_battery', 4, 3)->nullable()->default(null);
            $table->dateTime('aircraft_track_battery_timestamp')->nullable()->default(null);

            $table->unique('aircraft_particle_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('aircraft', function($table) {
            $table->dropColumn('aircraft_particle_id');
            $table->dropColumn('aircraft_track_udp_server');
            $table->dropColumn('aircraft_track_timer_1');
            $table->dropColumn('aircraft_track_timer_2');
            $table->dropColumn('aircraft_track_timer_3');
            $table->dropColumn('aircraft_track_timer_4');
            $table->dropColumn('aircraft_track_debug');
            $table->dropColumn('aircraft_track_last_status');
            $table->dropColumn('aircraft_track_status_timestamp');
            $table->dropColumn('aircraft_track_battery');
            $table->dropColumn('aircraft_track_battery_timestamp');
        });
    }
}
