<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubstitlesRequest;
use App\MovieSubstitle;
use App\Movie;
use App\Setting;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class SubstitleController extends Controller
{


    public function data()
    {


        return response()->json(MovieSubstitle::all(), 200);


    }


    // save a new video in AWS S3 or the videos disk of the storage
    public function store(SubstitlesRequest $request)
    {
        if ($request->hasFile('substitle')) {

            $settings = Setting::first();

            if ($settings->aws_s3_storage) {
                if (
                    $settings->aws_access_key_id != null &&
                    $settings->aws_secret_access_key != null &&
                    $settings->aws_default_region != null &&
                    $settings->aws_bucket != null
                ) {
                    config(['filesystems.disks.s3.key' => $settings->aws_access_key_id]);
                    config(['filesystems.disks.s3.secret' => $settings->aws_secret_access_key]);
                    config(['filesystems.disks.s3.region' => $settings->aws_default_region]);
                    config(['filesystems.disks.s3.bucket' => $settings->aws_bucket]);
                    
                    $filename = Storage::disk('s3')->put('', $request->substitle);
                    $url = Storage::disk('s3')->url($filename);

                    $data = [
                        'status' => 200,
                        'substitle_path' => $url,
                        'server' => config('app.name', 'AWS'),
                        'message' => 'successfully uploaded'
                    ];
                } else {
                    $data = [
                        'status' => 400,
                        'message' => 'could not be uploaded'
                    ];
                }
            } else {

                $extension = $request->substitle->getClientOriginalExtension();
                $filename = Storage::disk('substitles')->putFileAs('', $request->substitle, "$request->movie_title.$extension");
                
                //$filename = Storage::disk('substitles')->put('', $request->substitle);
                $data = [
                    'status' => 200,
                    'substitle_path' => $request->root() . '/api/substitle/' . $filename,
                    'server' => config('app.name', 'Own'),
                    'message' => 'successfully uploaded'
                ];
            }
        } else {
            $data = [
                'status' => 400,
                'message' => 'could not be uploaded'
            ];
        }

        return response()->json($data, $data['status']);
    }


    public function update(SubstitlesRequest $request, MovieSubstitle $subs)


    {

        $subs->fill($request->all());
        $subs->save();
        $data = [
            'status' => 200,
            'message' => 'successfully updated',
            'body' => $subs
        ];

        return response()->json($data, $data['status']);
    }


    // return an video from the videos disk of the storage
    public function show($filename)
    {

        $video = Storage::disk('substitles')->get("$filename");

        $mime = Storage::disk('substitles')->mimeType("$filename");

        return (new Response($video, 200))
            ->header('Content-Type', $mime);
    }
}
