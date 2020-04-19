<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Document;
use Illuminate\Http\Request;

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
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    DB::transaction(function() {
      //
    });
  }
}