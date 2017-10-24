<?php
require_once 'Pest.php';

class Gliding
{
    private $controller;
    private $key;
    private $pest;

    public function __construct($controller,$key=null)
    {
        $this->controller = $controller;
        if (null != $key)
            $this->key = $key;
        else
            $this->key = "1234567890123456";
        $this->pest  = new Pest( "http://" . $this->controller . "/api/v1/json/" . $this->key . "/");
    }
   
   public function getFlyingToday($club)
   {
      $result  = json_decode( $this->pest->get('flyingnow/' . $club), true );
      return $result;
   }
   
   public function getFlarmCode($glider)
   {
      $result  = json_decode( $this->pest->get('flarmcode/' . $glider), true );
      return $result;
   }
   
}