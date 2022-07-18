<?php

namespace App\Http\Controllers;

use App\Cast;
use App\MovieCast;
use App\Movie;
use App\Serie;
use App\Anime;
use App\Http\Requests\CastStoreRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use App\Http\Requests\CastUpdateRequest;
use App\Http\Requests\StoreImageRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Response;
use Validator;


class CastController extends Controller
{


    const STATUS = "status";
    const MESSAGE = "message";
    const VIEWS = "views";




    public function allCasters()
    {

    
        $casts = Cast::select('casts.id','casts.name','casts.profile_path','casts.gender')
        ->where('active', '=', 1)
        ->paginate(12);

        return response()
        ->json($casts, 200);

    }



    public function popularCasters()
    {

    
        $casts = Cast::select('casts.id','casts.name','casts.profile_path','casts.gender')
        ->where('active', '=', 1)
        ->orderByDesc('views')
        ->limit(15)
        ->get();

        
        return response()
        ->json(['popular_casters' => $casts], 200);

    }


    public function show($cast)
    {

        $casts = Cast::where('id', '=', $cast)->first();

        $casts->increment('views',1);
   
        return response()
        ->json($casts, 200);

    }



    public function showFilmographie($cast)
    {

        $movies = Movie::select('movies.title','movies.id','movies.poster_path','movies.vote_average','movies.updated_at')->addSelect(DB::raw("'movie' as type"))->whereHas('casters', function($genre) use ($cast) {
            $genre->where('cast_id', '=', $cast);
        })->orderByDesc('id');


        $animes = Anime::select('animes.name','animes.id','animes.poster_path','animes.vote_average','animes.updated_at')->addSelect(DB::raw("'anime' as type"))->whereHas('casters', function($genre) use ($cast) {
            $genre->where('cast_id', '=', $cast);
        })->orderByDesc('id');
      
        $series = Serie::select('series.name','series.id','series.poster_path','series.vote_average','series.updated_at')
        ->addSelect(DB::raw("'serie' as type"))->whereHas('casters', function($genre) use ($cast) {
            $genre->where('cast_id', '=', $cast);
        })->orderByDesc('id')
        ->union($movies)
        ->union($animes)
        ->orderBy("updated_at")
        ->paginate(12);

        $series->setCollection($series->getCollection()->makeHidden(['videos','substitles','casterslist','seasons','preview_path']));
        return $series;

      return response()
       ->json($series, 200);


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


     // update a movie in the database
     public function update(CastUpdateRequest $request, Cast $cast)
     {
        
        $cast->fill($request->all());
        $cast->save();

        $data = ['status' => 200, 'message' => 'successfully updated', 'body' => "Success"];

        return response()->json($data, $data['status']);
       
     }


    // returns all genres for the api
    public function datawebcaster() 
    {
        return response()->json(Cast::paginate(12), 200);
    }




    public function searchCast()
    {

        $query = \Request::get('q');
    	$movies = Cast::select('*')->whereRaw("name LIKE '%" . $query . "%'")
        ->orWhereRaw("original_name LIKE '%" . $query . "%'")
        ->get();

        return response()->json(['search_cast' => $movies], 200);

    }

    // returns all genres for the admin panel
    public function datacasters()
    {
        return response()->json(Cast::orderByDesc('created_at')
            ->paginate(12), 200);
    }

    // create a new genre in the database
    public function store(CastStoreRequest $request)
    {
        $find = new Cast();
        $find->fill($request->all());
        $find->save();

        $data = [
            'status' => 200,
            'message' => 'successfully created',
            'body' => $find
        ];

        return response()->json($data, $data['status']);
    }


    // delete a genre from the database
    public function destroy(Cast $genre)
    {
        if ($genre != null) {
            $genre->delete();
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



    // return all genres only with the id and name properties
    public function list()
    {

        return response()->json(['genres' => Cast::all('id', 'name')], 200);
    }



      // return an image from the movies folder of the storage
      public function getImg($filename)
      {
  
          $image = Storage::disk('casts')->get($filename);
  
          $mime = Storage::disk('casts')->mimeType($filename);
  
          return (new Response($image, 200))->header('Content-Type', $mime);
      }
  

}
