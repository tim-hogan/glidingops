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

    public function scopeValidAt($query, $dateTime) {
      return $query->orderBy('validfrom', 'desc')->where('validfrom', '<=', $dateTime);
    }

    public static function findByName($chargeName, $location, $organisation, $dateTime = null) {
      if(is_null($dateTime)) {
        $dateTime = new DateTime('now', $organisation->timeZone());
      }

      return Charge::where([
        'name' => $chargeName,
        'location' => $location,
        'org' => $organisation->id,
      ])->validAt($dateTime)->first();
    }
}
