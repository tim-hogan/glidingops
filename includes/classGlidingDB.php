<?php
require_once dirname(__FILE__) . '/classSQLPlus.php';
class GlidingDB extends SQLPlus 
{ 
    
    function __construct($params)
    {
        parent::__construct($params);
    }
    
    //*********************************************************************
    // Organistaion
    //*********************************************************************
    public function getOrganisation($org)
    {
        return $this->singlequery("SELECT * from organisations where id = ".intval($org));    
    }
    
    public function getOrgTimezone($org)
    {
        $tz='UTC';
        $o = $this->singlequery("SELECT timezone from organisations where id = ".intval($org));
        if($o)
            $tz=$o['timezone'];
        return $tz;
    }
    
    public function getOrgAircraftPrefix($org)
    {
        $o = $this->singlequery("SELECT aircraft_prefix from organisations where id = " . intval($org));
        if ($o)
            return $o['aircraft_prefix'];
        else
            return null;
    }
    
    public function getOrgLaunchCoords($org) 
    {
        $o = $this->singlequery("SELECT * from organisations where id = ".intval($org));
        if ($o)
        {
            $c = array();
            $c['lat'] = floatval($o['def_launch_lat']);
            $c['lon'] = floatval($o['def_launch_lon']);
            return $c;
        }
        else
            return null;
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
    
    public function allFlightsToday($org)
    {
        $dateTimeZone = new DateTimeZone($this->getOrgTimezone($org));
        $dateTime = new DateTime("now", $dateTimeZone);
        $dateStr = $dateTime->format('Ymd');
        
        $q = "SELECT * from flights where org = ".intval($org)." and localdate = '{$dateStr}'";
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
    
    public function getFlightWithNames($fid)
    {
        return $this->singlequery("select * , a.displayname as namePIC , b.displayname as nameP2 from flights LEFT JOIN members a ON a.id = flights.pic LEFT JOIN members b ON b.id = flights.p2 where flights.id = " . intval($fid)); 
    }
    
    public function getFlightOrg($fid)
    {
        $f = $this->singlequery("select org from flights where id = " . intval($fid));
        if ($f)
            return intval($f['org']);
        else
            return null;
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
    
    public function haveFlight($ts,$glider) 
    {
        $d = new DateTime($ts);
        $ts = $d->getTimestamp();
        $q = "Select * from flights where flights.glider = '".$glider."' and (flights.start/1000) <= ".$ts." and (flights.land/1000) >= " .$ts;
        $r = $this->query($q);
        if ($r->num_rows > 0)
            return true;
        return false;
    }
    
    //*********************************************************************
    // Spots
    //*********************************************************************
    public function getSpot($id)
    {
        return $this->singlequery("SELECT * from spots where id = " . intval($id));
    }
    
    public function getSpotByReg($org,$rego)
    {
        return $this->singlequery("SELECT * from spots where org = {$org} and rego_short = '{$rego}'");
    }
    
    public function updateSpotLastReq($org,$rego)
    {
        $dt = new DateTime;
        $strdt = $dt->format('Y-m-d H:i:s');
        return $this->update("update spots set lastreq = '{$strdt}' where org = {$org} and rego_short = '{$rego}'");
    }
    
    public function updateSpotLastListReq($org,$rego)
    {
        $dt = new DateTime;
        $strdt = $dt->format('Y-m-d H:i:s');
        return $this->update("update spots set lastlistreq = '{$strdt}' where org = {$org} and rego_short = '{$rego}'");
    }
    
    //*********************************************************************
    // Tracks
    //*********************************************************************
    public function createTrack($org,$rego,$gpstime,$gpstimemilli,$lat,$lon,$alt,$src='')
    {
        return $this->create("INSERT into tracks (org,glider,point_time,point_time_milli,lattitude,longitude,altitude,tracks_source) values (".$org.",'".$rego."','".$gpstime."',".intval($gpstimemilli).",".$lat.",".$lon.",".$alt.",'{$src}')");
    }
    
    public function numTracksForFlight($start,$end,$aircraft)
    {
        $q = "SELECT * from tracks where glider = '".$aircraft."' and point_time >= '".$start->format('Y-m-d H:i:s')."' and point_time <= '".$end->format('Y-m-d H:i:s')."' order by point_time";
        $r = $this->query($q);
        if (!$r) {$this->sqlError($q); return null;}
        return $r->num_rows;
    }
    
    public function getTracksForFlight($start,$end,$aircraft)
    {
        $q = "SELECT * from tracks where glider = '".$aircraft."' and point_time >= '".$start->format('Y-m-d H:i:s')."' and point_time <= '".$end->format('Y-m-d H:i:s')."' order by point_time";
        $r = $this->query($q);
        if (!$r) {$this->sqlError($q); return null;}
        return $r;
    }
    
    public function allTracks($order)
    {
        $q = "SELECT * from tracks " . $order;
        $r = $this->query($q);
        if (!$r) {$this->sqlError($q); return null;}
        return $r;
    }
    
    public function allTracksOlderThan($strdate)
    {
        $q = "SELECT * from tracks where point_time < '".$strdate."' order by point_time";
        $r = $this->query($q);
        if (!$r) {$this->sqlError($q); return null;}
        return $r;
    }
    
    public function deleteTrack($id)
    {
        return $this->delete("DELETE from tracks where id = " . intval($id));
    }
    
    public function deleteTracksOlderThan($strdate)
    {
        return $this->delete("DELETE from tracks where point_time < '".$strdate."'");
    }
}
?>