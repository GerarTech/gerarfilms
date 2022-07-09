<?php

namespace App\Http\Controllers;

use App\Ads;
use App\Http\Requests\AdsRequest;

class AdsController extends Controller
{




    const STATUS = "status";
    const MESSAGE = "message";
    const VIEWS = "views";


    public function data()
    {


     return response()->json(Ads::all(), 200);


    }


    public function ads()
    {

        $settings=Ads::inRandomOrder()->first();

        return response()->json($settings, 200);



    }


    public function store(AdsRequest $request)
    {


        if (isset($request->ads)) {

            $ads = new Ads();
            $ads->fill($request->ads);
            $ads->save();

            
            $data = [
                self::STATUS => 200,
                self::MESSAGE => 'successfully created',
                'body' => $ads
            ];
        } else {
            $data = [
                self::STATUS => 400,
                self::MESSAGE => 'could not be created',
            ];

        }

        return response()->json($data, $data[self::STATUS]);
    }


    public function destroy(Ads $ads)
    {
        if ($ads != null) {
            $ads->delete();
            $data = [
                self::STATUS => 200,
                self::MESSAGE => 'successfully deleted',
            ];
        } else {
            $data = [
                self::STATUS => 400,
                self::MESSAGE => 'could not be deleted',
            ];
        }

        return response()->json($data, $data[self::STATUS]);
    }


    public function update(AdsRequest $request, Ads $ads)


    {

        if ($ads != null) {


            $ads->fill($request->ads);
            $ads->save();
            $data = [
                self::STATUS => 200,
                self::MESSAGE => 'successfully updated',
                'body' => $ads
            ];


        } else {
            $data = [
                self::STATUS => 400,
                self::MESSAGE => 'could not be updated',
            ];
        }


        return response()->json($data, $data[self::STATUS]);
    }

}
