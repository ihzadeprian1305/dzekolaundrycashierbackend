<?php

namespace App\Http\Controllers;

use App\Models\UserLevel;
use App\Http\Requests\StoreUserLevelRequest;
use App\Http\Requests\UpdateUserLevelRequest;

class UserLevelController extends Controller
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
     * @param  \App\Http\Requests\StoreUserLevelRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserLevelRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\UserLevel  $userLevel
     * @return \Illuminate\Http\Response
     */
    public function show(UserLevel $userLevel)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\UserLevel  $userLevel
     * @return \Illuminate\Http\Response
     */
    public function edit(UserLevel $userLevel)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateUserLevelRequest  $request
     * @param  \App\Models\UserLevel  $userLevel
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserLevelRequest $request, UserLevel $userLevel)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\UserLevel  $userLevel
     * @return \Illuminate\Http\Response
     */
    public function destroy(UserLevel $userLevel)
    {
        //
    }
}
