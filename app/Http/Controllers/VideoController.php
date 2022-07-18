<?php

namespace App\Http\Controllers;

use FFMpeg;
use FFMpeg\Format\Video\X264;
use App\Jobs\VideoConversion;
use ProtoneMedia\LaravelFFMpeg\Filters\WatermarkFactory;
use App\Http\Requests\VideoRequest;
use App\Http\Requests\StreamingVideoRequest;
use App\Setting;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use FFMpeg\Filters\Video\VideoFilters;


class VideoController extends Controller
{



    const STATUS = "status";
    const MESSAGE = "message";
    const VIDEOS = "videos";


    // save a new video in AWS S3 or the videos disk of the storage
    public function store(VideoRequest $request)
    {
        if ($request->hasFile('video')) {

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

                    $filename = Storage::disk('s3')->put('', $request->video);
                    $url = Storage::disk('s3')->url($filename);

                    $data = [
                        self::STATUS => 200,
                        'video_path' => $url,
                        'server' => config('app.name', 'AWS'),
                        self::MESSAGE => 'successfully uploaded'
                    ];
                } else {
                    $data = [
                        self::STATUS => 400,
                        self::MESSAGE => 'could not be uploaded'
                    ];
                }
            } else if($settings->wasabi_storage) {
                if (
                    $settings->wasabi_access_key_id != null &&
                    $settings->wasabi_secret_access_key != null &&
                    $settings->wasabi_default_region != null &&
                    $settings->wasabi_bucket != null
                ) {
                    config(['filesystems.disks.wasabi.key' => $settings->wasabi_access_key_id]);
                    config(['filesystems.disks.wasabi.secret' => $settings->wasabi_secret_access_key]);
                    config(['filesystems.disks.wasabi.region' => $settings->wasabi_default_region]);
                    config(['filesystems.disks.wasabi.bucket' => $settings->wasabi_bucket]);

                    $filename = Storage::disk('wasabi')->put('', $request->video);
                    $url = Storage::disk('wasabi')->url($filename);

                    $data = [
                        self::STATUS => 200,
                        'video_path' => $url,
                        'server' => config('app.name', $settings->app_name),
                        self::MESSAGE => 'successfully uploaded'
                    ];
                } else {
                    $data = [
                        self::STATUS => 400,
                        self::MESSAGE => 'could not be uploaded'
                    ];
                }
            } else {


                $filename = Storage::disk('videos')->put('', $request->video);
                $data = [
                    self::STATUS => 200,
                    'video_path' => $request->root() . '/api/video/' . $filename,
                    'server' => config('app.name', 'EASYPLEX'),
                    self::MESSAGE => 'successfully uploaded'
                ];

                

            }
        } else {
            $data = [
                self::STATUS => 400,
                self::MESSAGE => 'could not be uploaded'
            ];
        }

        return response()->json($data, $data['status']);
    }





    public function Streamingstore(StreamingVideoRequest $request)
    {
        if ($request->hasFile('video')) {

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

                    $filename = Storage::disk('s3')->put('', $request->video);
                    $url = Storage::disk('s3')->url($filename);

                    $data = [
                        self::STATUS => 200,
                        'video_path' => $url,
                        'server' => config('app.name', 'AWS'),
                        self::MESSAGE => 'successfully uploaded'
                    ];
                } else {
                    $data = [
                        self::STATUS => 400,
                        self::MESSAGE => 'could not be uploaded'
                    ];
                }
            } else if($settings->wasabi_storage) {
                if (
                    $settings->wasabi_access_key_id != null &&
                    $settings->wasabi_secret_access_key != null &&
                    $settings->wasabi_default_region != null &&
                    $settings->wasabi_bucket != null
                ) {
                    config(['filesystems.disks.wasabi.key' => $settings->wasabi_access_key_id]);
                    config(['filesystems.disks.wasabi.secret' => $settings->wasabi_secret_access_key]);
                    config(['filesystems.disks.wasabi.region' => $settings->wasabi_default_region]);
                    config(['filesystems.disks.wasabi.bucket' => $settings->wasabi_bucket]);

                    $filename = Storage::disk('wasabi')->put('', $request->video);
                    $url = Storage::disk('wasabi')->url($filename);

                    $data = [
                        self::STATUS => 200,
                        'video_path' => $url,
                        'server' => config('app.name', $settings->app_name),
                        self::MESSAGE => 'successfully uploaded'
                    ];
                } else {
                    $data = [
                        self::STATUS => 400,
                        self::MESSAGE => 'could not be uploaded'
                    ];
                }
            } else {


                $filename = Storage::disk('videos')->put('', $request->video);
                $data = [
                    self::STATUS => 200,
                    'video_path' => $request->root() . '/api/video/' . $filename,
                    'server' => config('app.name', 'EASYPLEX'),
                    self::MESSAGE => 'successfully uploaded'
                ];

                

            }
        } else {
            $data = [
                self::STATUS => 400,
                self::MESSAGE => 'could not be uploaded'
            ];
        }

        return response()->json($data, $data['status']);
    }





    // return an video from the videos disk of the storage
    public function show($filename)
    {

        $video = Storage::disk(self::VIDEOS)->get("$filename");

        $mime = Storage::disk(self::VIDEOS)->mimeType("$filename");

        return (new Response($video, 200))
            ->header('Content-Type', $mime);
    }


    public function showFromMovieName($filename)
    {

        $video = Storage::disk(self::VIDEOS)->get("$filename");

        $mime = Storage::disk(self::VIDEOS)->mimeType("$filename");

        return (new Response($video, 200))
            ->header('Content-Type', $mime);
    }
}
