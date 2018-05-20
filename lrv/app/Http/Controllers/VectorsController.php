<?php

namespace App\Http\Controllers;

use Session;
use Redirect;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;

use App\Models\Vector;

class VectorsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        $vectors = $user->organisation->vectors();
        $locationFilter = Input::get('location');
        if($locationFilter && strlen($locationFilter) != 0) {
            $vectors = $vectors->where('location', $locationFilter);
        }

        return view('vectors.index', ['vectors' => $vectors->orderBy('location')->get()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('vectors.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        // validate
        $this->validate($request, [
            'location'       => 'required',
            'designation'    => 'required',
        ]);

        // store
        $vector = new Vector;
        $vector->location     = Input::get('location');
        $vector->designation  = Input::get('designation');
        $vector->organisation()->associate($user->organisation);
        $vector->save();

        // redirect
        Session::flash('message', 'Successfully created a new Vector!');
        return Redirect::to('app/vectors');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // delete
        $vector = Vector::find($id);
        $vector->delete();

        // redirect
        Session::flash('message', 'Successfully deleted one vector!');
        return Redirect::to('app/vectors');
    }
}
