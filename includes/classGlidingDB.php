<?php
require './includes/classSQLPlus.php';
class GlidingDB extends SQLPlus 
{ 
    
    function __construct($params)
    {
        parent::__construct($params);
    }
    
    //*********************************************************************
    // Organistaion
    //*********************************************************************
    public function getOrgTimezone($org)
    {
        $tz='UTC';
        $o = $this->singlequery("SELECT timezone from organisations where id = ".intval($org));
        if($o)
            $tz=$o['timezone'];
        return $tz;
    }
    
    //*********************************************************************
    // Flight Types
    //*********************************************************************
    public function getFlightType($strType)
    {
        $ret=-1.0;
        $ft = $this->singlequery("SELECT id from flighttypes where name = '".$strType."'");
        if ($ft) $ret = $ft['id'];
        return $ret;
    }
    
    public function getGlidingFlightType()
    {
        return $this->getFlightType('Glider');
    }

    
    //*********************************************************************
    // Flying
    //*********************************************************************
    public function flyingNow($org)
    {
        $dateTimeZone = new DateTimeZone($this->getOrgTimezone($org));
        $dateTime = new DateTime("now", $dateTimeZone);
        $dateStr = $dateTime->format('Ymd');
        $dateTimeNow = new DateTime("now");
        $flightTypeGlider = $this->getGlidingFlightType();
        
        $q = "SELECT flights.seq,flights.glider, b.displayname,c.displayname, (flights.start/1000) from flights LEFT JOIN members b ON b.id = flights.pic LEFT JOIN members c ON c.id = flights.p2 where flights.org = ".intval($org)." and flights.localdate=" . $dateStr . " and flights.type = ".$flightTypeGlider." and flights.deleted <> 1 and flights.start > 0 and flights.land=0 order by flights.seq ASC";
        $r = $this->query($q);
        if (!$r) {$this->sqlError($q); return null;}
        return $r;
    }
    
    public function completedToday($org)
    {
        $dateTimeZone = new DateTimeZone($this->getOrgTimezone($org));
        $dateTime = new DateTime("now", $dateTimeZone);
        $dateStr = $dateTime->format('Ymd');
        $dateTimeNow = new DateTime("now");
        $flightTypeGlider = $this->getGlidingFlightType();
        
        $q = "SELECT flights.seq,flights.glider, b.displayname,c.displayname, (flights.land-flights.start) from flights LEFT JOIN members b ON b.id = flights.pic LEFT JOIN members c ON c.id = flights.p2 where flights.org = ".intval($org)." and flights.localdate=" . $dateStr . " and flights.type = ".$flightTypeGlider." and flights.deleted <> 1 and flights.start > 0 and flights.land>0 order by flights.seq ASC";
        $r = $this->query($q);
        if (!$r) {$this->sqlError($q); return null;}
        return $r;
    }
    
    //*********************************************************************
    // Flight
    //*********************************************************************
    public function getFlight($fid)
    {
        return $this->singlequery("select * from flights where id = " . intval($fid));    
    }
    
    //*********************************************************************
    // Aircraft
    //*********************************************************************
    public function getAircraft($id)
    {
        return $this->singlequery("SELECT * from aircraft where id = " . intval($id));
    }
    
    public function getAircraftByRegShort($reg)
    {
        return $this->singlequery("select * from aircraft where rego_short = '" . $reg . "'");
    }
    
    public function getAircraftByParticleId($pid)
    {
        return $this->singlequery("SELECT * from aircraft where aircraft_particle_id = '" . $pid . "'");    
    }
    
    public function updateAircraftTrackStatus($aid,$status)
    {
        $d = new DateTime('now');
        return $this->update("UPDATE aircraft set aircraft_track_last_status = '".$status."', aircraft_track_status_timestamp = '".$d->format('Y-m-d H:i:s')."' where id = " . intval($aid));
    }
    
    public function updateAircraftTrackBattery($aid,$level)
    {
        $d = new DateTime('now');
        return $this->update("UPDATE aircraft set aircraft_track_battery = ".$level.", aircraft_track_battery_timestamp = '".$d->format('Y-m-d H:i:s')."' where id = " . intval($aid));
    }
    
    //*********************************************************************
    // Tracks
    //*********************************************************************
    public function createTrack($org,$rego,$gpstime,$gpstimemilli,$lat,$lon,$alt)
    {
        return $this->create("INSERT into tracks (org,glider,point_time,point_time_milli,lattitude,longitude,altitude) values (".$org.",'".$rego."','".$gpstime."',".intval($gpstimemilli).",".$lat.",".$lon.",".$alt.")");
    }
    
    public function getTracksForFlight($start,$end,$aircraft)
    {
        $q = "SELECT * from tracks where glider = '".$aircraft."' and point_time >= '".$start->format('Y-m-d H:i:s')."' and point_time <= '".$end->format('Y-m-d H:i:s')."' order by point_time";
        $r = $this->query($q);
        if (!$r) {$this->sqlError($q); return null;}
        return $r;
    }
}
?>