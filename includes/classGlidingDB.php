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
}
?>