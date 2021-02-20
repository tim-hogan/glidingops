<?php
class Rolling
{

    public static function checkRate($DB,$name)
    {
        $rateHigh = false;
        $dt = new DateTime();
        $debug = false;

        if ($roll = $DB->getRollingByName($name) )
        {
            if ($debug)
                error_log("---Start rolling debug {$name}---");
            if ($roll['rolling_entity_disabled'])
            {
                if ($debug)
                    error_log("   Rolling entity disabled");
                $rateHigh = true;
                //Need to check time stamp
                $dtDisable = new DateTime($roll['rolling_disable_timestamp']);
                if ($dt->getTimestamp() > ($dtDisable->getTimestamp() + $roll['rolling_disable_seconds']))
                {
                    $DB->resetRolling($name);
                    $rateHigh = false;
                    if ($debug)
                        error_log("   Rolling entity ree-nabled");
                }
            }

            $a = array();
            $mod = intval($roll['rolling_modulus']);
            $idx = intval($roll['rolling_idx']);

            if ($debug)
            {
                error_log("   Idx: {$idx}");
                error_log("   Mod: {$mod}");
                error_log("   Counters: {$roll['rolling_counters']}");
            }


            if ($roll['rolling_counters'] && strlen($roll['rolling_counters']) > 0)
                $a = json_decode($roll['rolling_counters'],true);
            if (count($a) >= $mod )
            {
                //We need to check if the access rate is too high.  Oldest is idx, most recent is idx-1 mod
                $j = $idx-1;
                if ($j < 0 )
                    $j += $mod;

                if ( floatval($dt->getTimestamp() - $a[$idx]) == 0)
                    $rate = 100000;
                else
                    $rate = floatval($mod+1) / floatval($dt->getTimestamp() - $a[$idx]);
                //$rate = floatval($mod) / floatval($a[$j] - $a[$idx]);
                //Create and audit
                if ($rate > $roll['rolling_target'])
                {
                    if ($debug)
                        error_log("   Rate: {$rate} Oldest {$a[$idx]} Current {$dt->getTimestamp()}");
                    $DB->markRollingDisabled($name);
                    $DB->createRollingAudit($name,$rate);
                    $rateHigh = true;
                }
            }
            if ($debug)
                error_log("---End rolling debug---");

            $a[$idx] = $dt->getTimestamp();
            $idx = ($idx+1) % $mod;
            $DB->updateRolling($name,$idx,json_encode($a));
        }
        else
        {
            //Create it
            $DB->createRolling($name);
        }
        return $rateHigh;
    }
}
?>