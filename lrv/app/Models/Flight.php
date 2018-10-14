<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Aircraft;

class Flight extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'flights';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function picMember()
    {
        return $this->belongsTo('App\Models\Member', 'pic');
    }

    public function p2Member()
    {
        return $this->belongsTo('App\Models\Member', 'p2');
    }

    public function towPilotMember()
    {
        return $this->belongsTo('App\Models\Member', 'towpilot');
    }

    public function billingOption()
    {
        return $this->belongsTo('App\Models\BillingOption', 'billing_option');
    }

    public function towAircraft()
    {
        return $this->belongsTo('App\Models\Aircraft', 'towplane');
    }

    public function gliderAircraft()
    {
        return Aircraft::where(['rego_short' => $this->glider, 'org' => $this->org])->first();
    }

    public function launchType()
    {
        return $this->belongsTo('App\Models\LaunchType', 'launchtype');
    }

    public function flightType()
    {
        return $this->belongsTo('App\Models\FlightType', 'type');
    }

    public function organisation()
    {
        return $this->belongsTo('App\Models\Organisation', 'org');
    }

    public function getTowlandDateTime()
    {
        $date = new \DateTime();
        $date->setTimestamp(intval(floor($this->towland / 1000)));
        return $date;
    }

    public function getStartDateTime()
    {
        $date = new \DateTime();
        $date->setTimestamp(intval(floor($this->start / 1000)));
        return $date;
    }

    public function getLandDateTime()
    {
        $date = new \DateTime();
        $date->setTimestamp(intval(floor($this->land / 1000)));
        return $date;
    }

    public function getTowDuration()
    {
        if(empty($this->towland)) {
            return 0;
        }
        return self::durationRoundedToMinutes($this->towland - $this->start);
    }

    public function getFlightDuration()
    {
        return self::durationRoundedToMinutes($this->land - $this->start);
    }

    public function getFullComments()
    {
        $comments = $this->comments;
        if ($this->flightType == FlightType::checkFlightType()) {
            if (strlen($comments) > 0 ) {
              $comments .= " ";
            }
            $comments .= "Tow plane check flight";
        }

        if ($this->flightType == FlightType::retrieveFlightType()) {
            if (strlen($comments) > 0 ) {
              $comments .= " ";
            }
            $comments .= "Retrieve Flight";
        }

        return $comments;
    }

    public function hasTracks()
    {
        $strStart = $this->getStartDateTime()->format('Y-m-d H:i:s');
        $strEnd   = $this->getLandDateTime()->format('Y-m-d H:i:s');

        $tracks = Track::where('glider', $this->glider)
                    ->where('point_time', '>' , $strStart)
                    ->where('point_time', '<', $strEnd);

        if(!$tracks->get()->isEmpty()) {
            return true;
        }

        $tracks = ArchivedTrack::where('glider', $this->glider)
                    ->where('point_time', '>' , $strStart)
                    ->where('point_time', '<', $strEnd);

        if(!$tracks->get()->isEmpty()) {
            return true;
        }

        return false;
    }

    private function durationRoundedToMinutes($durationInMillies) {
      $durationInSeconds = intval($durationInMillies / 1000);
      $durationRoundedToMinutes  = ($durationInSeconds - $durationInSeconds%60);
      return $durationRoundedToMinutes;
    }
}