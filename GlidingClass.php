<?php
require 'Pest.php';

class Gliding
{
    private $controllerGops;
    private $controllerGNZ;
    private $pestGOps;
    private $pestGNZ;

    public function __construct( $controller1,$controller2 = null)
    {
        $this->controllerGops = $controller1;
        $this->pestGOps  = new Pest( "http://" . $this->controllerGops . "/");
        if (null != $controller2)
        {
            $this->controllerGNZ = $controller2;
            $this->pestGNZ  = new Pest( "http://" . $this->controllerGNZ . "/api/v1/");
        }
    }
   
   public function getFlyingToday($club)
   {
      $result  = json_decode( $this->pestGOps->get('http://glidingops.com/apiglidjsonv1.php?r=1234567890123456/flyingnow/' . $club), true );
      return $result;
   }
   
   public function getGNZGliderData($glider)
   {
      $G = '';
      switch (strlen($glider))
      {
      case 2:
         $G = "ZK-G" . strtoupper($glider);
         break;
      case 3:
         $G = "ZK-" . strtoupper($glider);
         break;
      default:
         $G = strtoupper($glider);
         break;
      }
      $result  = json_decode( $this->pestGNZ->get('aircraft/' . $G), true );
      return $result;
   }
   
   public function getGNZFlarmData($day,$hexcode)
   {
      $result  = json_decode( $this->pestGNZ->get('tracking/' . $day . '/' . $hexcode . '/pings' ), true );
      return $result;
  }
}