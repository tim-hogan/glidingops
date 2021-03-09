<?php
require './includes/classSQLPlus.php';
class trackDB extends SQLPlus
{

    function __construct($params)
    {
        parent::__construct($params);
    }

    //*********************************************************************
    // aircraft
    //*********************************************************************
    public function getAircraft($id)
    {
         return $this->singlequery("select * from aircraft  where idaircraft = " . intval($id));
    }

    public function getAircraftWithType($id)
    {
         return $this->singlequery("select * from aircraft left join aircraft_type a on a.idaircraft_type = aircraft.aircraft_type where idaircraft = " . intval($id));
    }

    public function getAircraftByICAO($ICAO)
    {
         return $this->singlequery("select * from aircraft where airacraft_ICAO = '" . $ICAO . "'");
    }

    public function createAircraft($ICAO)
    {
         return $this->create("insert into aircraft (airacraft_ICAO) values ('".$ICAO."')");
    }

    public function updateAircraft($id,$reg,$type)
    {
        if (intval($type) > 0)
            return $this->update("update aircraft set aircraft_reg = '".$reg."', aircraft_type = ".intval($type)." where idaircraft = " . intval($id));
        else
            return $this->update("update aircraft set aircraft_reg = '".$reg."' where idaircraft = " . intval($id));
    }

    public function allAircraft($order = '')
    {
        $q = "select * from aircraft " . $order;
        $r = $this->query($q);
        if (!$r) {$this->sqlError($q); return null;}
        return $r;
    }

    //*********************************************************************
    // aircraft_type
    //*********************************************************************
    public function getAircraftType($id)
    {
         return $this->singlequery("select * from aircraft_type  where idaircraft_type = " . intval($id));
    }

    public function allAircraftType($order = '')
    {
        $q = "select * from aircraft_type " . $order;
        $r = $this->query($q);
        if (!$r) {$this->sqlError($q); return null;}
        return $r;
    }

    //*********************************************************************
    // info
    //*********************************************************************
    public function createInfoId($iacoid,$data)
    {
        $d = new DateTime();
        return $this->create("insert into info (info_aircraft_id,info_timestamp,info_type,info_strdata) values (".$iacoid.",'".$d->format('Y-m-d H:i:s')."','id','".$data."')");
    }

    public function getFlightId($aid,$postime)
    {
        $d = new DateTime($postime);
        $d->setTimestamp($d->getTimestamp()- (30 * 60)); // Go back 30 minutes
        return $this->singlequery("select * from info where info_aircraft_id = " . intval($aid) . " and info_timestamp > '".$d->format('Y-m-d H:i:s')."' order by info_timestamp DESC LIMIT 1");
    }

    //*********************************************************************
    // position
    //*********************************************************************
    public function createPosition($iacoid,$lat,$lon,$alt,$odd,$latcpr,$loncpr,$calctype)
    {
        $d = new DateTime();
        $f='false';
        if ($odd)
            $f = 'true';
        $cm = 'none';
        if (strlen($calctype) > 0)
            $cm = $calctype;
        return $this->create("insert into position (position_timestamp,position_aircraft,position_lat,position_lon,position_alt,position_odd_flag,position_lat_cpr,position_lon_cpr,position_calc_method) values ('".$d->format('Y-m-d H:i:s')."',".$iacoid.",".$lat.",".$lon.",".$alt.",".$f.",".$latcpr.",".$loncpr.",'".$cm."')");
    }

    public function allPositions()
    {
        $q = "select * from position order by position_timestamp";
        $r = $this->query($q);
        if (!$r) {$this->sqlError($q); return null;}
        return $r;
    }

    public function allPositionsFor($aid)
    {
        $q = "select * from position where position_aircraft = " . intval($aid) . " order by position_timestamp";
        $r = $this->query($q);
        if (!$r) {$this->sqlError($q); return null;}
        return $r;
    }

    public function findLastEvenPosition($aid)
    {
        return $this->singlequery("select * from position where position_aircraft = " . intval($aid) . " and position_odd_flag = false order by position_timestamp DESC LIMIT 1");
    }

    public function lastPositionsForAircraft($aid,$timeback)
    {
        $d = new DateTime('now');
        $intspec = "PT" . $timeback . "S";
        $d->sub(new DateInterval($intspec));
        return $this->singlequery("select * from position left join aircraft a on a.idaircraft = position_aircraft where position_timestamp > '".$d->format('Y-m-d H:i:s')."' and position_aircraft = " . intval($aid) . " order by position_timestamp DESC LIMIT 1");
    }

    public function viewLastPositionsForAircraft($aid,$timeback,$limit)
    {
        $d = new DateTime('now');
        $intspec = "PT" . $timeback . "S";
        $d->sub(new DateInterval($intspec));
        return $this->singlequery("select * from position left join aircraft a on a.idaircraft = position_aircraft where position_timestamp > '".$d->format('Y-m-d H:i:s')."' and position_aircraft = " . intval($aid) . " order by position_timestamp DESC LIMIT " . intval($limit));
    }

    //*********************************************************************
    // velocity
    //*********************************************************************
    public function createVelocity($iacoid,$gnd_speed,$v_dir,$v_speed)
    {
        $d = new DateTime();
        return $this->create("insert into velocity (velocity_timestamp,velocity_aircraft,velocity_grnd_speed,velocity_verticle_dir,velocity_verticle_speed) values ('".$d->format('Y-m-d H:i:s')."',".$iacoid.",".$gnd_speed.",'".$v_dir."',".$v_speed.")");
    }

    public function velocityForAircraft($aid,$timestamp)
    {
        $d = new DateTime($timestamp);
        //we want + / - 10 seconds
        $d1 = new DateTime();
        $d1->setTimestamp($d->getTimestamp() - 10);
        $d2 = new DateTime();
        $d2->setTimestamp($d->getTimestamp() + 10);

        return $this->singlequery("select * from velocity where velocity_timestamp >= '".$d1->format('Y-m-d H:i:s')."' and velocity_timestamp <= '".$d2->format('Y-m-d H:i:s')."' and velocity_aircraft = ". intval($aid) ." order by velocity_timestamp DESC LIMIT 1");
    }

    //*********************************************************************
    // Vehcile
    //*********************************************************************
    public function getVehilce($id)
    {
        return $this->singlequery("select * from vehicle where idvehicle = ". intval($id) );
    }

    public function getVehilceByDevId($devid)
    {
        return $this->singlequery("select * from vehicle where vehicle_particle_id = '".$devid."'");
    }

    public function updateVehilceStatus($id,$status)
    {
        $d = new DateTime('now');
        return $this->update("update vehicle set vehicle_last_seen = '".$d->format('Y-m-d H:i:s')."', vehicle_last_status = '".$status."' where idvehicle = " . intval($id));
    }

    public function updateVehicleLastHello($id,$version=null)
    {
        $d = new DateTime('now');
        if (is_null($version))
            return $this->update("update vehicle set vehicle_last_hello = '".$d->format('Y-m-d H:i:s')."', vehicle_last_seen = '".$d->format('Y-m-d H:i:s')."', vehicle_last_status = 'hello' where idvehicle = " . intval($id));
        else
            return $this->update("update vehicle set vehicle_last_hello = '".$d->format('Y-m-d H:i:s')."', vehicle_last_seen = '".$d->format('Y-m-d H:i:s')."', vehicle_last_status = 'hello' , vehicle_particle_version = {$version} where idvehicle = " . intval($id));
    }

    public function updateVehilceBattery($id,$level)
    {
        $d = new DateTime('now');
        return $this->update("update vehicle set vehicle_battery_timestamp = '".$d->format('Y-m-d H:i:s')."', vehicle_battery_level = ".$level." where idvehicle = " . intval($id));
    }

    public function updateVehilceSeq($id,$seq)
    {
        return $this->update("update vehicle set vehicle_seq_complete = ".$seq." where idvehicle = " . intval($id));
    }

    public function allVehilces($order = '')
    {
        $q = "select * from vehicle " . $order;
        $r = $this->query($q);
        if (!$r) {$this->sqlError($q); return null;}
        return $r;
    }

    //*********************************************************************
    // Track
    //*********************************************************************
    public function createTrack($vid,$lat,$lon,$alt,$timestamp,$udp = false)
    {
        if ($vid != 0 && $lat != 0.0 && $lon != 0.0)
        {
            $q = '';
            if ($udp)
                $q = "insert into track (track_vehicle,track_timestamp,track_lat,track_lon,track_alt,track_from_udp) values (".intval($vid).",'".$timestamp."',".$lat.",".$lon.",".$alt.",true)";
            else
                $q = "insert into track (track_vehicle,track_timestamp,track_lat,track_lon,track_alt,track_from_udp) values (".intval($vid).",'".$timestamp."',".$lat.",".$lon.",".$alt.",false)";
            $r = $this->query($q);
            if (!$r)
            {
                if ($this->errno != 1062)
                {
                    $this->sqlError($q);
                    return false;
                }
            }
            return true;
        }
    }

    public function lastTrackForVehicle($vid)
    {
        return $this->singlequery("select * from track where track_vehicle = " . intval($vid) . " order by track_timestamp DESC LIMIT 1");
    }

    public function previousTrackForVehicle($vid,$strTime)
    {
        return $this->singlequery("select * from track where track_vehicle = " . intval($vid) . " and track_timestamp < '{$strTime}' order by track_timestamp DESC LIMIT 1");
    }


    public function allTracksForVehicle($vid)
    {
        $q = "select * from track where track_vehicle = " . intval($vid);
        $r = $this->query($q);
        if (!$r) {$this->sqlError($q); return null;}
        return $r;
    }

    public function allTracksForVehicleNoTrip($vid)
    {
        $q = "select * from track where track_vehicle = " . intval($vid) . " and track_trip is null order by track_timestamp";
        $r = $this->query($q);
        if (!$r) {$this->sqlError($q); return null;}
        return $r;
    }

    public function allTracksForVehicleTrip($vid,$tripid)
    {
        $q = "select * from track where track_vehicle = " . intval($vid) . " and track_trip = ".intval($tripid)." order by track_timestamp";
        $r = $this->query($q);
        if (!$r) {$this->sqlError($q); return null;}
        return $r;
    }

    public function prevTrackForVehilce($track)
    {
        return $this->singlequery("select * from track where track_vehicle = {$track['track_vehicle']} and track_timestamp < '{$track['track_timestamp']}' order by track_timestamp desc limit 1");
    }

    public function updateTrackTrip($t,$tripid)
    {
        $this->update("update track set track_trip = " . intval($tripid) . " where track_vehicle = " . intval($t['track_vehicle']) . " and track_timestamp = '".$t['track_timestamp']."'");
    }

    //*********************************************************************
    // Trip
    //*********************************************************************
    public function getTripByVehStart($vid,$strStart)
    {
        return $this->singlequery("select * from trip where trip_vehicle = " . intval($vid)." and trip_start = '{$strStart}'");
    }

    public function createTrip($vid,$strStart)
    {
        $q = "insert into trip (trip_vehicle,trip_start) values (".intval($vid).",'{$strStart}')";
        $r = $this->query($q);
        if (!$r) {$this->sqlError($q); return null;}
        return $this->getTripByVehStart($vid,$strStart);
    }

    public function allTripsForVehilce($vid)
    {
        $q = "select * from trip where trip_vehicle = " . intval($vid) . " order by trip_start";
        $r = $this->query($q);
        if (!$r) {$this->sqlError($q); return null;}
        return $r;
    }


}
?>