<?php
require_once './includes/classSQLPlus.php';
class TracksDB extends SQLPlus 
{ 
    
    function __construct($params)
    {
        parent::__construct($params);
    }
 
    //*********************************************************************
    // Tracks
    //*********************************************************************  
    public function numTracksForFlight($start,$end,$aircraft)
    {
        $q = "SELECT * from tracksarchive where glider = '".$aircraft."' and point_time >= '".$start->format('Y-m-d H:i:s')."' and point_time <= '".$end->format('Y-m-d H:i:s')."' order by point_time";
        $r = $this->query($q);
        if (!$r) {$this->sqlError($q); return null;}
        return $r->num_rows;
    }
    
    public function getTracksForFlight($start,$end,$aircraft)
    {
        $q = "SELECT * from tracksarchive where glider = '".$aircraft."' and point_time >= '".$start->format('Y-m-d H:i:s')."' and point_time <= '".$end->format('Y-m-d H:i:s')."' order by point_time";
        $r = $this->query($q);
        if (!$r) {$this->sqlError($q); return null;}
        return $r;
    }
}
?>