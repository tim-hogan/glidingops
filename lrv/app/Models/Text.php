<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Text extends Model
{
  /**
   * The table associated with the model.
   *
   * @var string
   */
  protected $table = 'texts';
  protected $primaryKey = 'txt_id';

  /**
   * Indicates if the model should be timestamped.
   *
   * @var bool
   */
  public $timestamps = false;
}