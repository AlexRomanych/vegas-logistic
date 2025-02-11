<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Worker\WorkerCollection;
use App\Http\Resources\Worker\WorkerResource;
use App\Models\Worker\Worker;
use Illuminate\Http\Request;

class WorkerController extends Controller
{

    public function workers()
    {
        return new WorkerCollection(
            Worker::query()
                ->orderBy('surname')
                ->orderBy('name')
                ->with('cellItem')
                ->get()
        );
    }


    public function model($code1C)
    {
        return new WorkerResource(Worker::query()->find($code1C));
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
