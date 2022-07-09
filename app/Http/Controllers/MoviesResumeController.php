<?php

namespace App\Http\Controllers;

use App\Resume;
use Illuminate\Http\Request;


class MoviesResumeController extends Controller
{


    public function data()
    {


        return response()->json(Resume::all(), 200);

    }


    public function sendResume(Request $request)
    {

        $this->validate($request, [
            'user_resume_id' => 'required',
            'tmdb' => 'required',
            'resumeWindow' => 'required',
            'resumePosition' => 'required',
            'movieDuration' => 'required',
            'deviceId' => 'required'
        ]);

        $resume = Resume::create([
            'user_resume_id' => request('user_resume_id'),
            'tmdb' => request('tmdb'),
            'resumeWindow' => request('resumeWindow'),
            'resumePosition' => request('resumePosition'),
            'movieDuration' => request('movieDuration'),
            'deviceId' => request('deviceId')

        ]);


        $data = ['status' => 200, 'message' => 'created successfully', 'body' => $resume];

        return response()->json($data, 200);

    }




    public function show($movie)
    {


        $movie = Resume::where('tmdb', '=', $movie)->orWhere('id', '=', $movie)->orderByDesc('created_at')->first();
        
        return response()->json($movie, 200);


    }


    public function destroy(Resume $resume)


    {


        if ($resume != null) {
            $resume->delete();

            $data = ['status' => 200, 'message' => 'successfully removed',];
        } else {
            $data = ['status' => 400, 'message' => 'could not be deleted',];
        }

        return response()->json($data, $data['status']);
    }
}
