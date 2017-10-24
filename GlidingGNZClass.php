<?php
require_once 'Pest.php';

class GNZ
{
    private $controller;
    private $pest;

    public function __construct( $controller)
    {
            $this->controller = $controller;
            $this->pest  = new Pest( "http://" . $this->controller . "/api/v1/");
    }
   
   public function getGliderData($glider)
   {
      $result = null;
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
      try 
      {
        $result  = json_decode( $this->pest->get('aircraft/' . $G), true );
      }
      
      catch (Exception $e)
      {
        $result = array("Status" => "Error Exception"); 
      }
      return $result;
   }
   
   public function getFlarmData($day,$hexcode)
   {
      $result  = json_decode( $this->pest->get('tracking/' . $day . '/' . $hexcode . '/pings' ), true );
      return $result;
  }
}