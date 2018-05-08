<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vector extends Model
{
  public function organisation()
  {
    return $this->belongsTo('App\Models\Organisation');
  }

  public function scopeForLocation($query, $location)
  {
    return $query->where('location', $location);
  }
}

/*

$vector = new App\Models\Vector()
$vector->organisation()->associate(App\Models\Organisation::first())
$vector->designation = '03'
$vector->location = 'Papawai'
$vector->save()

$org->vectors()->forLocation('Papawai')

*/
