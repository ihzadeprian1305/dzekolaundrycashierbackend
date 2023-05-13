<?php

namespace App\Http\Controllers;

use App\Models\UserDatum;
use App\Http\Requests\StoreUserDatumRequest;
use App\Http\Requests\UpdateUserDatumRequest;

class UserDatumController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreUserDatumRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserDatumRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\UserDatum  $userDatum
     * @return \Illuminate\Http\Response
     */
    public function show(UserDatum $userDatum)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\UserDatum  $userDatum
     * @return \Illuminate\Http\Response
     */
    public function edit(UserDatum $userDatum)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateUserDatumRequest  $request
     * @param  \App\Models\UserDatum  $userDatum
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserDatumRequest $request, UserDatum $userDatum)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\UserDatum  $userDatum
     * @return \Illuminate\Http\Response
     */
    public function destroy(UserDatum $userDatum)
    {
        //
    }
}
