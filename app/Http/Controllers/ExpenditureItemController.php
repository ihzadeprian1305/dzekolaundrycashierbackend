<?php

namespace App\Http\Controllers;

use App\Models\ExpenditureItem;
use App\Http\Requests\StoreExpenditureItemRequest;
use App\Http\Requests\UpdateExpenditureItemRequest;

class ExpenditureItemController extends Controller
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
     * @param  \App\Http\Requests\StoreExpenditureItemRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreExpenditureItemRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ExpenditureItem  $expenditureItem
     * @return \Illuminate\Http\Response
     */
    public function show(ExpenditureItem $expenditureItem)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ExpenditureItem  $expenditureItem
     * @return \Illuminate\Http\Response
     */
    public function edit(ExpenditureItem $expenditureItem)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateExpenditureItemRequest  $request
     * @param  \App\Models\ExpenditureItem  $expenditureItem
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateExpenditureItemRequest $request, ExpenditureItem $expenditureItem)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ExpenditureItem  $expenditureItem
     * @return \Illuminate\Http\Response
     */
    public function destroy(ExpenditureItem $expenditureItem)
    {
        //
    }
}
