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
    // Users
    //*********************************************************************
    public function getUser($id)
    {
        return $this->p_singlequery("select * from users where id = ?","i",$id);
    }

    public function getUserWithMember($id)
    {
        return $this->p_singlequery("select * from users left join members a on a.id = member where users.id = ?","i",$id);
    }

    //*********************************************************************
    // Members
    //*********************************************************************
    public function getMember($id)
    {
        return $this->p_singlequery("select * from members where id = ?","i",$id);
    }

    public function getMemberWithClass($id)
    {
        return $this->p_singlequery("select * from members left join membership_class a ON a.id = members.class where id = ?","i",$id);
    }

    public function IsMemberTowy($memid)
    {
        $mid = intval($memid);
        $roletow = $this->getRoleIdByName('Tow Pilot');
        if ( $this->rows_in_table("role_member","where member_id = {$mid} and role_id = {$roletow}") > 0)
            return true;
        return false;
    }

    public function IsMemberInstructor($memid)
    {
        $mid = intval($memid);
        $r = $this->allRolesLike('Instructor');
        while ($role = $r->fetch_array(MYSQLI_ASSOC))
        {
        if ( $this->rows_in_table("role_member","where member_id = {$mid} and role_id = {$role['id']}") > 0)
            return true;
        return false;
    }

    //*********************************************************************
    // membership_class
    //*********************************************************************
    public function getMembershipClass($id)
    {
        return $this->p_singlequery("select * from membership_class where id = ?","i",$id);
    }

    public function getMembershipClassByClass($org,$class)
    {
        return $this->p_singlequery("select * from membership_class where org = ? and class = ?","is",$org,$class);
    }

    public function getJuniorClassId($org)
    {
        if ($v = getMembershipClassByClass($org,'Junior') )
            return $v['id'];
        return null;
    }

    //*********************************************************************
    // roles
    //*********************************************************************
    public function getRole($id)
    {
        return $this->p_singlequery("select * from roles where id = ?","i",$id);
    }

    public function getRoleIdByName($name)
    {
        if ($role = $this->p_singlequery("select * from roles where name = ?","s",$name) )
            return $role['id'];
        return null;
    }

    public function allRolesLike($name)
    {
        $l = "%{$name}%";
        $q = "SELECT * from roles where name LIKE ? ";
        $r = $this->p_query($q,"s",$l);
        if (!$r) {$this->sqlError($q); return null;}
        return $r;
    }

    //*********************************************************************
    // Messages
    //*********************************************************************
    public function getLastOrgMessage($org)
    {
        $o = intval($org);
        return $this->singlequery("select * from messages where org = {$o} order by create_time desc limit 1");
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

    public function allGliderFlightsForMember($memid)
    {
        $gliderFlightType = $this->getGlidingFlightTypeId();
        $r = $this->p_query("select * from flights where type = {$gliderFlightType} and (flights.pic= ? or flights.p2 = ?)","ii",$memid,$memid);
    }

    //*********************************************************************
    // Aircraft
    //*********************************************************************
    public function getAircraft($id)
    {
        return $this->singlequery("SELECT * from aircraft where id = " . intval($id));
    }

    public function getClubGliders($org)
    {
        $q = "select * from aircraft where org = ? and club_glider > 0";
        $r = $this->p_query($q,"i",$org);
        if (!$r) {$this->sqlError($q); return null;}
        return $r;
    }

    public function getAircraftByRegShort($reg)
    {
        return $this->singlequery("select * from aircraft where rego_short = '" . $reg . "'");
    }

    public function getAircraftByParticleId($pid)
    {
        return $this->singlequery("SELECT * from aircraft where aircraft_particle_id = '" . $pid . "'");
    }

    public function getAircraftBySpotId($sid)
    {
        return $this->singlequery("SELECT * from aircraft where spot_id = '{$sid}'");
    }

    public function getGliderModel($org,$short_rego)
    {
        if($a = $this->p_singlequery("SELECT * FROM aircraft WHERE org = ? and rego_short = ?","is",$org,$short_rego) )
            return $a['make_model'];
        return null;
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
    // Launchtypes
    //*********************************************************************
    public function getLaunchTypeId($type)
    {
        $t = $this->p_singlequery("select * from launchtype where name = ?","s",$type);
        if ($t)
            return $t['id'];
        return null;
    }

    public function getTowLaunchTypeId()
    {
        return $this->getLaunchTypeId('Tow Plane');
    }

    public function getSelfLaunchTypeId()
    {
        return $this->getLaunchTypeId('Self Launch');
    }

    public function getWinchLaunchTypeId()
    {
        return $this->getLaunchTypeId('Winch');
    }

    //*********************************************************************
    // Flighttypes
    //*********************************************************************
    public function getFlightTypeId($type)
    {
        $t = $this->p_singlequery("select * from flighttypes where name = ?","s",$type);
        if ($t)
            return $t['id'];
        return null;
    }

    public function getGlidingFlightTypeId()
    {
        return $this->getLaunchTypeId('Glider');
    }

    public function getCheckFlightTypeId()
    {
        return $this->getLaunchTypeId('Tow plane check flight');
    }

    public function getRetrieveFlightTypeId()
    {
        return $this->getLaunchTypeId('Tow plane retrieve');
    }

    public function getLandingChargeFlightTypeId()
    {
        return $this->getLaunchTypeId('Landing Charge');
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