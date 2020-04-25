<?php

namespace App\Models;
use Spatie\MediaLibrary\Media as BaseMedia;
use Carbon\Carbon;

class Document extends BaseMedia 
{
  // TODO: find a way to make expires_at autocast to/from Carbon date
  // protected $fillable = ['expires_at'];
  // protected $casts = ['expires_at' => 'date'];

  public function isExpired(): bool
  {
    return false;
    // return $this->expires_at->isPast();
  }
}