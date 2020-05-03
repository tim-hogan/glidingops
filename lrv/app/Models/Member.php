<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\HasMedia\Interfaces\HasMedia;

class Member extends Model implements HasMedia
{
    use HasMediaTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'members';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that should be visible in array/json representation.
     *
     * @var array
     */
    // protected $visible = ['id', 'member_id', 'org', 'firstname', 'surname', 'displayname'];

    /**
     * The roles that belong to the meber.
     */
    public function roles()
    {
        return $this->belongsToMany('App\Models\Role', 'role_member');
    }

    public function organisation()
    {
        return $this->belongsTo('App\Models\Organisation', 'org');
    }

    public function flightsAsPIC()
    {
        return $this->hasMany('App\Models\Flight', 'pic');
    }

    public function flightsAsP2()
    {
        return $this->hasMany('App\Models\Flight', 'p2');
    }

    public function membershipStatus()
    {
        return $this->belongsTo('App\Models\MembershipStatus', 'status');
    }

    public function latestDocuments()
    {
      $latestDocuments = $this->media()
        ->groupBy('collection_name')
        ->select(\DB::raw("collection_name, max(order_column) AS order_column, count(*) AS version_count"));
      return Document::joinSub($latestDocuments->getQuery(), 'latest_documents', function($join){
          $join->on('documents.collection_name', '=', 'latest_documents.collection_name');
          $join->on('documents.order_column', '=', 'latest_documents.order_column');
        })
        ->orderBy('documents.collection_name', 'asc');
    }
}
