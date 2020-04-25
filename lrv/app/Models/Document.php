<?php

namespace App\Models;
use Spatie\MediaLibrary\Media as BaseMedia;
use Carbon\Carbon;

class Document extends BaseMedia 
{
  public function getDates()
  {
    return ['created_at', 'upated_at', 'expires_at'];
  }

  public function isExpired(): bool
  {
    if(!is_null($this->expires_at)) {
      return $this->expires_at->isPast();
    } else {
      return false;
    }
  }
}