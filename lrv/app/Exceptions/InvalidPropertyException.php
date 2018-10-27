<?php

namespace App\Exceptions;

use App\Exceptions\GopsException;

class InvalidPropertyException extends GopsException
{
  private $propertyName;

  public function __construct($propertyName) {
    $this->propertyName = $propertyName;
    parent::__construct("Invalid {$this->propertyName}");
  }

  public function getPropertyName() {
    return $this->propertyName;
  }
}