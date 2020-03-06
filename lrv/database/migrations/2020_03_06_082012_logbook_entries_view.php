<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class LogbookEntriesView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<SQL
CREATE VIEW logbook_entries AS
SELECT
  org,
  glider,
  localdate,
  SUM((land - start) DIV 60000) as duration_minutes,
  SUM(case when lt.acronym = 'A' then 1 else 0 end) as aerotow_launches,
  SUM(case when lt.acronym = 'W' then 1 else 0 end) as winch_launches,
  COUNT(*) as launches FROM flights
INNER JOIN launchtypes lt ON lt.id = launchtype
GROUP BY glider, localdate, org;
SQL;
        DB::statement($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP VIEW logbook_entries');
    }
}
