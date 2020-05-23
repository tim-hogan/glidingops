<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\HasMedia\Interfaces\HasMedia;
use App\Models\Document;

class Member extends Model implements HasMedia
{
    use HasMediaTrait;

    protected $fillable = [
      'medical_expire',
      'icr_expire',
      'bfr_expire'
    ];

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

    public function getLatestInCollection($collection_name) {
      return $this->media()->where('collection_name', $collection_name)->orderBy('order_column', 'desc')->first();
    }

    public function updateExpiryFieldsFromDocuments() {
      $latest_medical = $this->getLatestInCollection(Document::COLLECTION_MEDICAL);
      $latest_bfr = $this->getLatestInCollection(Document::COLLECTION_BFR);

      $this->update([
        'medical_expire' => ($latest_medical === null) ? null : $latest_medical->issued_at,
        'icr_expire' => null,
        'bfr_expire' => ($latest_bfr === null) ? null : $latest_bfr->issued_at,
      ]);
    }
}
