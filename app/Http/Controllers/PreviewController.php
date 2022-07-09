<?php

namespace App\Http\Controllers;

use App\Http\Requests\PreviewRequest;
use App\Http\Requests\StoreImageRequest;
use App\Jobs\SendNotification;
use App\Preview;
use App\Setting;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;


class PreviewController extends Controller
{


    public function latest()
    {

        
        $movies = Preview::orderByDesc('created_at')
            ->limit(10)
            ->get();

        return response()
            ->json(['previews' => $movies], 200);

    }
    

// returns all upcoming for api
    public function index()
    {
        return response()->json(Preview::orderByDesc('id')->paginate(12), 200);
    }


    // returns all upcoming for admin panel
    public function data()
    {
        return response()->json(Preview::orderByDesc('created_at')
        ->get(), 200);
    }

    // create a new upcoming in the database
    public function store(PreviewRequest $request)
    {
        if (isset($request->preview)) {

            $preview = new Preview();
            $preview->fill($request->preview);
            $preview->save();
            $data = [
                'status' => 200,
                'message' => 'successfully created',
                'body' => $preview
            ];
        } else {
            $data = [
                'status' => 400,
                'message' => 'could not be created',
            ];
        }


        return response()->json($data, $data['status']);
    }

    // returns a specific upcoming
    public function show(Preview $preview)
    {
        return response()->json($preview, 200);
    }

    // update a upcoming in the database
    public function update(PreviewRequest $request, Preview $preview)
    {
        if ($preview != null) {

            $preview->fill($request->preview);
            $preview->save();
            $data = [
                'status' => 200,
                'message' => 'successfully updated',
                'body' => $preview
            ];
        } else {
            $data = [
                'status' => 400,
                'message' => 'could not be updated',
            ];
        }

        return response()->json($data, $data['status']);
    }

    // delete a upcoming in the database
    public function destroy(Preview $preview)
    {
        if ($preview != null) {
            $preview->delete();
            $data = [
                'status' => 200,
                'message' => 'successfully deleted',
            ];
        } else {
            $data = [
                'status' => 400,
                'message' => 'could not be deleted',
            ];
        }

        return response()->json($data, $data['status']);
    }

    // save a new image in the upcoming folder of the storage
    public function storeImg(StoreImageRequest $request)
    {
        if ($request->hasFile('image')) {
            $filename = Storage::disk('preview')->put('', $request->image);
            $data = [
                'status' => 200,
                'image_path' => $request->root() . '/api/preview/image/' . $filename,
                'message' => 'successfully uploaded'
            ];
        } else {
            $data = [
                'status' => 400,
            ];
        }

        return response()->json($data, $data['status']);
    }

    // return an image from the upcoming folder of the storage
    public function getImg($filename)
    {

        $image = Storage::disk('preview')->get($filename);

        $mime = Storage::disk('preview')->mimeType($filename);

        return (new Response($image, 200))
            ->header('Content-Type', $mime);
    }


}

