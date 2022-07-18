<?php

namespace App\Http\Controllers;

use App\Http\Requests\Request;
use App\Http\Requests\FeaturedRequest;
use App\Http\Requests\FeaturedUpdateRequest;
use App\Http\Requests\StoreImageRequest;
use App\Http\Requests\MiniPosterStoreImageRequest;
use App\Jobs\SendNotification;
use App\Featured;
use App\Setting;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;


class FeaturedController extends Controller
{


    
    const STATUS = "status";
    const MESSAGE = "message";
    const VIEWS = "views";

    

// returns all Featured for api
    public function index()
    {
        return response()->json(Featured::orderByDesc('id')->paginate(12), 200);
    }


    public function featured()
    {

        $settings = Setting::first();

        $featured = Featured::orderBy('position')->orderByDesc('updated_at')
            ->limit($settings->featured_home_numbers)
            ->get();

        return response()
            ->json(['featured' => $featured], 200);
    }


    // returns all Featured for admin panel
    public function data()
    {
        return response()->json(Featured::orderByDesc('created_at')
        ->get(), 200);
    }

    // create a new Featured in the database
    public function store(FeaturedRequest $request)
    {
        if (isset($request->featured)) {

            $featured = new Featured();
            $featured->fill($request->featured);
            $featured->save();
            $data = [
                'status' => 200,
                'message' => 'successfully created',
                'body' => $featured
            ];
        } else {
            $data = [
                'status' => 400,
                'message' => 'could not be created',
            ];
        }


        return response()->json($data, $data['status']);
    }

    // returns a specific Featured
    public function show(Featured $featured)
    {
        return response()->json($featured, 200);
    }

    // update a Featured in the database
    public function update(FeaturedUpdateRequest $request, Featured $featured)
    {

        if ($featured != null) {

            $featured->fill($request->featured);
            $featured->save();
            $data = [
                'status' => 200,
                'message' => 'successfully updated',
                'body' => $featured
            ];
        } else {
            $data = [
                'status' => 400,
                'message' => 'could not be updated',
            ];
        }

        return response()->json($data, $data['status']);
    }

    // delete a Featured in the database
    public function destroy($featured)
    {
        if ($featured != null) {

            Featured::find($featured)->delete();

            $data = ['status' => 200, 'message' => 'successfully deleted',];
        } else {
            $data = ['status' => 400, 'message' => 'could not be deleted',];
        }

        return response()->json($data, 200);

    
    }

    // save a new image in the Featured folder of the storage
    public function storeImg(MiniPosterStoreImageRequest $request)
    {
        if ($request->hasFile('image')) {
            $filename = Storage::disk('movies')->put('', $request->image);
            $data = [
                'status' => 200,
                'image_path' => $request->root() . '/api/featured/image/' . $filename,
                'message' => 'successfully uploaded'
            ];
        } else {
            $data = [
                'status' => 400,
            ];
        }

        return response()->json($data, $data['status']);
    }

    // return an image from the Featured folder of the storage
    public function getImg($filename)
    {

        $image = Storage::disk('movies')->get($filename);

        $mime = Storage::disk('movies')->mimeType($filename);

        return (new Response($image, 200))
            ->header('Content-Type', $mime);
    }


}

