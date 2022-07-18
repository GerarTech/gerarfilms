<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpcomingRequest;
use App\Http\Requests\StoreImageRequest;
use App\Jobs\SendNotification;
use App\Upcoming;
use App\Setting;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;


class UpcomingController extends Controller
{
    

// returns all upcoming for api
    public function index()
    {
        return response()->json(Upcoming::orderByDesc('id')->paginate(12), 200);
    }


    public function latest()
    {

        $streaming = Upcoming::orderByDesc('created_at')
            ->limit(10)
            ->get();

        return response()
            ->json(['upcoming' => $streaming], 200);
    }


    // returns all upcoming for admin panel
    public function data()
    {
        return response()->json(Upcoming::orderByDesc('created_at')
        ->get(), 200);
    }

    // create a new upcoming in the database
    public function store(UpcomingRequest $request)
    {
        if (isset($request->upcoming)) {

            $upcoming = new upcoming();
            $upcoming->fill($request->upcoming);
            $upcoming->save();
            $data = [
                'status' => 200,
                'message' => 'successfully created',
                'body' => $upcoming
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
    public function show(Upcoming $upcoming)
    {
        return response()->json($upcoming, 200);
    }

    // update a upcoming in the database
    public function update(UpcomingRequest $request, Upcoming $upcoming)
    {
        if ($upcoming != null) {

            $upcoming->fill($request->upcoming);
            $upcoming->save();
            $data = [
                'status' => 200,
                'message' => 'successfully updated',
                'body' => $upcoming
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
    public function destroy(Upcoming $upcoming)
    {
        if ($upcoming != null) {
            $upcoming->delete();
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
            $filename = Storage::disk('upcoming')->put('', $request->image);
            $data = [
                'status' => 200,
                'image_path' => $request->root() . '/api/upcoming/image/' . $filename,
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

        $image = Storage::disk('upcoming')->get($filename);

        $mime = Storage::disk('upcoming')->mimeType($filename);

        return (new Response($image, 200))
            ->header('Content-Type', $mime);
    }


}

