<?php

namespace App\Http\Controllers;

use App\Network;
use App\MovieCast;
use App\Movie;
use App\Serie;
use App\Anime;
use App\Http\Requests\NetworkRequest;
use App\Http\Requests\GenreRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use App\Http\Requests\StoreImageRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Response;
use Validator;


class NetworkController extends Controller
{


    const STATUS = "status";
    const MESSAGE = "message";
    const VIEWS = "views";



    // return all movies of a genre
    public function showNetworks($network)
    {

        $series = Serie::orderByDesc('created_at')->whereHas('networks', function ($query) use ($network) {
            $query->where('network_id', '=', $network);
        })->select('series.name','series.id','series.poster_path','series.vote_average','series.subtitle')->addSelect(DB::raw("'serie' as type"));


        $movies = Movie::orderByDesc('created_at')->whereHas('networks', function ($query) use ($network) {
            $query->where('network_id', '=', $network);
        })->select('movies.title','movies.id','movies.poster_path','movies.vote_average','movies.subtitle')->addSelect(DB::raw("'movie' as type"));


        $animes = Anime::orderByDesc('created_at')->whereHas('networks', function ($query) use ($network) {
            $query->where('network_id', '=', $network);
        })->select('animes.name','animes.id','animes.poster_path','animes.vote_average','animes.subtitle')->addSelect(DB::raw("'anime' as type"));

        $query = $movies
        ->union($series)
        ->union($animes)
        ->paginate(12);

        $query->setCollection($query->getCollection()->makeHidden(['substype','downloads','casterslist',
        'casters','seasons','overview','backdrop_path','preview_path','videos'
        ,'substitles','vote_count','popularity','runtime','release_date','imdb_external_id','hd','pinned','preview',
    'networkslist','networks']));
        return $query;
      
        return response()->json($query, 200);
    }



     // save a new image in the movies folder of the storage
     public function storeImg(StoreImageRequest $request)
     {
         if ($request->hasFile('image')) {
             $filename = Storage::disk('casts')->put('', $request->image);
             $data = ['status' => 200, 'image_path' => $request->root() . '/api/casts/image/' . $filename, 'message' => 'successfully uploaded'];
         } else {
             $data = ['status' => 400, 'message' => 'could not be uploaded'];
         }
 
         return response()->json($data, $data['status']);
     }


 // returns all genres for the api
 public function index()
 {
     return response()->json(Network::All(), 200);
 }

 // returns all genres for the admin panel
 public function datawebnetworks()
 {
     return response()->json(Network::All(), 200);
 }

 // create a new genre in the database
 public function store(NetworkRequest $request)
 {
     $genre = new Network();
     $genre->fill($request->all());
     $genre->save();

     $data = [
         'status' => 200,
         'message' => 'successfully created',
         'body' => $genre
     ];

     return response()->json($data, $data['status']);
 }


 public function destroy($genre)
    {
        if ($genre != null) {
            Network::find($genre)->delete();
            $data = [
                'status' => 200,
                'message' => 'successfully deleted'
            ];
        } else {
            $data = [
                'status' => 400,
                'message' => 'could not be deleted'
            ];
        }

        return response()->json($data, $data['status']);
    }

 // update a genre in the database
 public function update(NetworkRequest $request, Network $network)
 {


        if ($network != null) {


            $network->fill($request->all());
            $network->save();
            $data = [
                self::STATUS => 200,
                self::MESSAGE => 'successfully updated',
                'body' => $network
            ];


        } else {
            $data = [
                self::STATUS => 400,
                self::MESSAGE => 'could not be updated',
            ];
        }


        return response()->json($data, $data[self::STATUS]);
    
 }

 // return all genres only with the id and name properties
 public function list()
 {

     //return response()->json(['networks' => Network::all('id', 'name','logo_path')], 200);

     return response()->json(['networks' => Network::select('networks.id',
     'networks.name','networks.logo_path')->limit(10)->get()], 200);

 }


 public function lists()
 {
     return response()->json(['networks' => Network::select('networks.id',
     'networks.name','networks.logo_path')->get()], 200);

 }


      // return an image from the movies folder of the storage
      public function getImg($filename)
      {
  
          $image = Storage::disk('casts')->get($filename);
  
          $mime = Storage::disk('casts')->mimeType($filename);
  
          return (new Response($image, 200))->header('Content-Type', $mime);
      }
  

}
