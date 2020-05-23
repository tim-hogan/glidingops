<?php

namespace App\Models;
use Spatie\MediaLibrary\Media as BaseMedia;
use Carbon\Carbon;

class Document extends BaseMedia 
{
  const COLLECTION_BFR     = 'BFR';
  const COLLECTION_ICR     = 'ICR';
  const COLLECTION_MEDICAL = 'Medical Certificate';

  const PREDEFINED_COLLECTIONS = [
    Document::COLLECTION_BFR,
    Document::COLLECTION_ICR,
    Document::COLLECTION_MEDICAL,
    'A Certificate',
    'B Certificate',
    'QGP'
  ];


  public function getDates()
  {
    return ['created_at', 'upated_at', 'issued_at'];
  }

  public function isExpired(): bool
  {
    switch ($this->collection_name) {
      case Document::COLLECTION_BFR:
        return $this->issued_at->startOfDay()->isBefore(Carbon::now()->subYears(2)->startOfDay());
      default:
        return false;
    }
  }

  public static function documentCollections()
  {
    $customCollections = Document::select('collection_name')->whereNotIn('collection_name', Document::PREDEFINED_COLLECTIONS)
                            ->distinct()->get()
                            ->map(function($d) { return $d->collection_name; } );
    return collect(Document::PREDEFINED_COLLECTIONS)->merge($customCollections);
  }
}