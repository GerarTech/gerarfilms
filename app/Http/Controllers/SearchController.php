<?php

namespace App\Http\Controllers;


use App\Http\Requests\Request;
use App\Movie;
use App\Serie;
use App\Livetv;
use App\Anime;
use App\User;
use App\Cast;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    // returns all the movies, animes and livetv that match the search
    public function index($query)
    {


        $movies = Movie::select('*')->whereRaw("title LIKE '%" . $query . "%'")
        ->orWhereRaw("original_name LIKE '%" . $query . "%'")->where('active', '=', 1)
        ->addSelect(DB::raw("'Movie' as type"))->limit(10)->get();

        $series = Serie::select('*')->whereRaw("name LIKE '%" . $query . "%'")
        ->orWhereRaw("original_name LIKE '%" . $query . "%'")->where('active', '=', 1)
        ->addSelect(DB::raw("'Serie' as type"))->limit(10)->get();

        $stream = Livetv::select('*')->whereRaw("name LIKE '%" . $query . "%'")->where('active', '=', 1)
        ->addSelect(DB::raw("'Streaming' as type"))->limit(10)->get();

        $anime = Anime::select('*')->whereRaw("name LIKE '%" . $query . "%'")
        ->orWhereRaw("original_name LIKE '%" . $query . "%'")->where('active', '=', 1)
        ->addSelect(DB::raw("'Anime' as type"))->limit(10)->get();

        //$cast = Cast::select('*')->where('name', 'LIKE', "%$query%")->orWhere('original_name', 'LIKE', "%$query%")->where('active', '=', 1)->limit(50)
        //->addSelect(DB::raw("'caster' as type"))->get();

        $array = array_merge($movies->makeHidden(['casters','casterslist','networkslist','downloads','networks'])
        ->toArray(), $series->makeHidden(['casters','casterslist','networkslist','downloads','networks','seasons','episodes'])->toArray(),
        
        $stream->makeHidden('seasons','episodes')->toArray(), $anime->makeHidden(['casters','casterslist','networkslist','downloads','networks','seasons','episodes'])->toArray());


        return response()->json(['search' => $array], 200);
    }




    public function searchFeatured()
    {

        $query = \Request::get('q');
    	$movies = Movie::select('*')->whereRaw("title LIKE '%" . $query . "%'")
        ->orWhereRaw("original_name LIKE '%" . $query . "%'")
        ->where('active', '=', 1)
        ->addSelect(DB::raw("'Movie' as type"))->limit(50)->get();

        $series = Serie::select('*')->whereRaw("name LIKE '%" . $query . "%'")
        ->orWhereRaw("original_name LIKE '%" . $query . "%'")->where('active', '=', 1)
        ->addSelect(DB::raw("'Serie' as type"))->limit(50)->get();


        $anime = Anime::select('*')->whereRaw("name LIKE '%" . $query . "%'")
        ->orWhereRaw("original_name LIKE '%" . $query . "%'")->where('active', '=', 1)
        ->addSelect(DB::raw("'Anime' as type"))->limit(50)->get();

        $livetv = Livetv::select('*')->whereRaw("name LIKE '%" . $query . "%'")
        ->addSelect(DB::raw("'Streaming' as type"))->limit(50)->get();

        $array = array_merge($movies->toArray(),
         $series->makeHidden('seasons','episodes')->toArray()
         ,$anime->makeHidden('seasons','episodes')->toArray(),$livetv->toArray());

        return response()->json(['search' => $array], 200);

    }



    public function searchCasts()
    {
    	$query = \Request::get('q');

        $casts = Cast::select('*')->where('name', 'LIKE', "%$query%")->limit(50)->get();

    	return response()->json([ 'casts' => $casts ],Response::HTTP_OK);
    }


    public function searchMovies()
    {
    	$query = \Request::get('q');
        $movies = Movie::select('*')->where('title', 'LIKE', "%$query%")->limit(10)->get();

    	return response()->json([ 'movies' => $movies ],Response::HTTP_OK);
    }


    public function searchSeries()
    {
    	$query = \Request::get('q');
        $movies = Serie::select('*')->where('name', 'LIKE', "%$query%")->limit(10)->get();

    	return response()->json([ 'series' => $movies ],Response::HTTP_OK);
    }


    
    public function searchAnimes()
    {
    	$query = \Request::get('q');
        $movies = Anime::select('*')->where('name', 'LIKE', "%$query%")->limit(10)->get();

    	return response()->json([ 'animes' => $movies ],Response::HTTP_OK);
    }



    public function searchStreaming()
    {
    	$query = \Request::get('q');
        $movies = Livetv::select('*')->where('name', 'LIKE', "%$query%")->limit(10)->get();

    	return response()->json([ 'streaming' => $movies ],Response::HTTP_OK);
    }

    public function searchUsers()
    {
    	$query = \Request::get('q');
        $movies = User::select('*')->where('email', 'LIKE', "%$query%")->get();

    	return response()->json([ 'users' => $movies ],Response::HTTP_OK);
    }


}