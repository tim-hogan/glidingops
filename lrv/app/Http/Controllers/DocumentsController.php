<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Document;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Redirect;
use Log;
use DB;

class DocumentsController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  // public function index()
  // {
  //   //
  // }

  /**
   * Download a document.
   *
   * @return \Illuminate\Http\Response
   */
  public function show(Member $member, Document $document)
  {
    return $document;
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  // public function create()
  // {
  //   $model = new Member();
  //   $route = ['members.store'];
  //   return view('members.edit')->with(['model' => $model, 'route' => $route, 'method' => 'post']);
  // }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Models\Member  $member
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request, Member $member)
  {
    $documentType = $request->input('documentType');
    $timezone = $request->input('tz', '+12:00');
    $documentExpiresAtInput = $request->input('documentExpiresAt', '');
    
    if(!empty($documentExpiresAtInput)) {      
      $documentExpiresAt = Carbon::createFromFormat('Y-m-d', $documentExpiresAtInput, $timezone)->tz('UTC')->toDateString();      
    }
    
    DB::transaction(function() use ($member, $documentExpiresAt, $documentType) {    
      $newDocument = $member->addMediaFromRequest('documentFile')->toMediaCollection($documentType);
      if(isset($documentExpiresAt)) {
        $newDocument->expires_at = $documentExpiresAt;
        $newDocument->save();
      }
    });

    return Redirect::route('members.edit', [$member->id]);
  }
}