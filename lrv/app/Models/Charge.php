<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Model;

class Charge extends Model
{
    protected $table = 'charges';
    public $timestamps = false;

    public function organisation() {
      return $this->belongsTo('App\Models\Organisation', 'org');
    }

    public static function findByName($chargeName, $location, $organisation, $now = null) {
      if(is_null($now)) {
        $now = new DateTime('now', $organisation->timeZone());
      }

      return Charge::where([
        'name' => $chargeName,
        'location' => $location,
        'org' => $organisation->id,
      ])->orderBy('validfrom', 'desc')->where('validfrom', '<=', $now)->first();
    }
}
