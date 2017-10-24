<?php
class ogn
{
    private $host;
    public function __construct($controller = null)
    {
        
        
        if (null != $controller)
            $this->host = $controller;
        else
            $this->host = 'live.glidernet.org';
    }
    
    public function getCurrentFlarms()
    {

        $url = "http://" . $this->host . "/lxml.php?a=0&z=0";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        $data = curl_exec($ch);
        curl_close($ch);

        $doc = new DOMDocument();
        if (!$doc->loadXML($data) )
        {
            echo "Could not load XML<br/>";
            echo $data;
        }
        else
        {
            $locs = array();
            $gliders = $doc->getElementsByTagName('m');
            
            foreach ($gliders AS $glider) 
            {
                $str = $glider->attributes->getNamedItem('a')->nodeValue;
                $parms =  str_getcsv ($str);
                
                $upd = array();
                $upd['time'] = $parms[5];
                $upd['lat'] = $parms[0];
                $upd['lon'] = $parms[1];
                $upd['alt'] = $parms[4];
                $upd['speed'] = $parms[8];
        
                $locs[$parms[12]] = $upd;
        
            }
            
            return json_encode($locs);
        }
    }
}
?>