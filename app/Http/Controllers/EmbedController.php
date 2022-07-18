<?php

namespace App\Http\Controllers;

use App\Embed;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EmbedController extends Controller
{




    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param Embed $embed
     * @return Response
     */
    public function show(Embed $embed)
    {
       
        $embed = $embed->code;

        return view('embed', compact('embed'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Embed $embed
     * @return Response
     */
    public function edit(Embed $embed)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Embed $embed
     * @return Response
     */
    public function update(Request $request, Embed $embed)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Embed $embed
     * @return Response
     */
    public function destroy(Embed $embed)
    {

        
        //
    }
}
