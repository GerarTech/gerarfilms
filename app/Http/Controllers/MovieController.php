<?php

namespace App\Http\Controllers;

use App\Network;
use App\MovieNetwork;
use RuntimeException;
use Illuminate\Support\Facades\Auth;
use App\Embed;
use App\Genre;
use App\Cast;
use App\Http\Requests\MovieStoreRequest;
use App\Http\Requests\MovieUpdateRequest;
use App\Http\Requests\StoreImageRequest;
use App\Jobs\SendNotification;
use App\Movie;
use App\MovieGenre;
use App\MovieSubstitle;
use App\MovieVideo;
use App\MovieDownload;
use App\MovieCast;
use App\Serie;
use App\Anime;
use App\Livetv;
use App\Setting;
use App\User;
use App\Featured;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use File;
use Illuminate\Support\Facades\Crypt;
use Spatie\ResponseCache\Facades\ResponseCache;
use App\Http\ClearsResponseCache;
use ChristianKuri\LaravelFavorite\Traits\Favoriteable;
use BeyondCode\Comments\Comment;


class MovieController extends Controller
{

    const STATUS = "status";
    const MESSAGE = "message";
    const VIDEOS = "videos";

    use ClearsResponseCache,Favoriteable;




    public function __construct()
    {
        $this->middleware('doNotCacheResponse', ['only' => ['deletecomment','moviecomment','addcomment']]);
    }



  // return movie details 
    public function show($movie)
    {


        $movie = Movie::query()
        ->with(['casters.cast' => function ($query) {
            $query->select('id', 'name','original_name','profile_path');
        }])->where('id', '=', $movie)->first()->makeHidden(['casters','networks']);

        $movie->increment('views',1);
        
        return response()->json($movie, 200);


    }


    // return all the movies for the admin panel
    public function web()
    
    {

        return response()->json(Movie::with(['genres','casters'])->orderByDesc('created_at')
            ->paginate(12), 200);
    }

     // return all the movies for the admin panel
     public function search()
    
     {
 
         return response()->json(Movie::orderByDesc('created_at')
             ->paginate(12), 200);
     }
 

    // create a new movie in the database
    public function store(MovieStoreRequest $request)
    {
        $movie = new Movie();
        $movie->fill($request->movie);
        $movie->save();

        $this->onStoreMovieDownloads($request,$movie);
        $this->onStoreMovieVideo($request,$movie);
        $this->onStoreMovieCasters($request,$movie);
        $this->onStoreMovieGenres($request,$movie);
        $this->onStoreMovieSubstitles($request,$movie);
        $this->onStoreMovieNetworks($request,$movie);

        if ($request->notification) {
            $this->dispatch(new SendNotification($movie));
        }


        $data = ['status' => 200, 'message' => 'created successfully', 'body' => $movie];

        return response()->json($data, $data['status']);
    }



    public function onStoreMovieNetworks($request,$movie) {

        if ($request->movie['networks']) {
            foreach ($request->movie['networks'] as $network) {
                $find = Network::find($network['id']);
                if ($find == null) {
                    $find = new Network();
                    $find->fill($network);
                    $find->save();
                }
                $serieNetwork = new MovieNetwork();
                $serieNetwork->network_id = $network['id'];
                $serieNetwork->movie_id = $movie->id;
                $serieNetwork->save();
            }
        }
    }
    
    public function onStoreMovieSubstitles($request,$movie) {

      
        if ($request->linksubs) {
            foreach ($request->linksubs as $substitle) {
                $movieSubstitle = new MovieSubstitle();
                $movieSubstitle->fill($substitle);
                $movieSubstitle->movie_id = $movie->id;
                $movieSubstitle->save();
            }
        }

    }


    

    public function onStoreMovieDownloads($request,$movie) {

        if ($request->linksDownloads) {
            foreach ($request->linksDownloads as $link) {

                $movieVideo = new MovieDownload();
                $movieVideo->fill($link);
                $movieVideo->movie_id = $movie->id;
                $movieVideo->save();
            }
        }
    }

    public function onStoreMovieVideo($request,$movie) {

        if ($request->links) {
            foreach ($request->links as $link) {

                $movieVideo = new MovieVideo();
                $movieVideo->fill($link);
                $movieVideo->movie_id = $movie->id;
                $movieVideo->save();
            }
        }

    }



    public function onStoreMovieGenres($request,$movie) {

        if ($request->movie['genres']) {
            foreach ($request->movie['genres'] as $genre) {
                $find = Genre::find($genre['id']);
                if ($find == null) {
                    $find = new Genre();
                    $find->fill($genre);
                    $find->save();
                }
                $movieGenre = new MovieGenre();
                $movieGenre->genre_id = $genre['id'];
                $movieGenre->movie_id = $movie->id;
                $movieGenre->save();
            }
        }

    }

    public function onStoreMovieCasters($request,$movie) {

        if ($request->movie['casterslist']) {
            foreach ($request->movie['casterslist'] as $cast) {
                $find = Cast::find($cast['id']);
                if ($find == null) {
                    $find = new Cast();
                    $find->fill($cast);
                    $find->save();
                }
                $movieGenre = new MovieCast();
                $movieGenre->cast_id = $cast['id'];
                $movieGenre->movie_id = $movie->id;
                $movieGenre->save();
            }
        }

    }



    
    public function moviecomment($movie)
    {

        $movie = Movie::where('id', '=', $movie)->first();

        $comments = $movie->comments;

        return response()
            ->json(['comments' => $comments], 200);
    }



    public function addcomment(Request $request)
    {


        $user = Auth::user();


        $this->validate($request, [
            'comments_message' => 'required',
            'movie_id' => 'required'
        ]);
    
        $movie = Movie::where('id', '=', $request->movie_id)->first();

        $comment = $movie->commentAsUser($user, $request->comments_message);

        return response()->json($comment, 200);

    }



     public function deletecomment($movie,Request $request)
    {

        $user = Auth::user();

        if ($movie != null) {

            Comment::find($movie)->delete();

            $data = ['status' => 200, 'message' => 'successfully deleted',];
        } else {
            $data = ['status' => 400, 'message' => 'could not be deleted',];
        }

        
        return response()->json($data, 200);

    }
    


    public function deletecommentweb($movie)
    {

        if ($movie != null) {

            $movie = Comment::where('commentable_id', '=', $movie)->first();
            $movie->delete();
            $data = ['status' => 200, 'message' => 'successfully deleted',];
        } else {
            $data = [
                'status' => 400,
                'message' => 'could not be deleted',
            ];
        }

        return response()->json($data, $data['status']);
    }

    
    public function addtofav($movie,Request $request)
    {

    $movie = Movie::where('id', '=', $movie)->first()->addFavorite($request->user()->id);
 
            return response()->json("Success", 200);

    }


    public function removefromfav($id,Request $request)
    {

        $movie =   Movie::where('id', '=', $id)->first()->removeFavorite($request->user()->id);


        return response()->json("Added", 200);


    }


    public function isMovieFavorite($id,Request $request)
    {

        $movie = Movie::where('id', '=', $id)->first();

        if($movie->isFavorited($request->user()->id)) {

            $data = ['status' => 200, 'status' => 1,];

        }else {
            
            $data = ['status' => 400, 'status' => 0,];
        }

        return response()->json($data, 200);
    }


   


    public function userfav()
    {
         
        
        $user = Auth::user();
        $user->favorite(Movie::class);
        
        return response()->json($user, 200);


    }






    
    public function comments($movie)
    {


        $movie = Movie::where('id', '=', $movie)->first();

        $comments = $movie->comments;

        
        return response()
            ->json(['comments' => $comments], 200);

    }



    public function share($type,$movie)
    {


        if($type == "movie") {

            $movies = Movie::select('movies.title','movies.id')->addSelect(DB::raw("'movie' as type"))
            ->where('id', '=', $movie)
            ->get();


            return response()->json($movies->makeHidden(['casterslist','casters','seasons','genres','genreslist','overview','backdrop_path','preview_path','videos'
            ,'substitles','vote_average','vote_count','popularity','runtime','release_date','imdb_external_id','hd','pinned','preview'
            ,'networks','downloads','networkslist']), 200);

        }else {

            $series = Serie::select('series.name','series.id')->addSelect(DB::raw("'movie' as type"))
            ->where('id', '=', $movie)
            ->first();


            return response()->json($series->makeHidden(['casterslist','casters','seasons','genres','genreslist','overview','backdrop_path','preview_path','videos'
            ,'substitles','vote_average','vote_count','popularity','runtime','release_date','imdb_external_id','hd','pinned','preview'
            ,'networks','downloads','networkslist']), 200);

        }

    }



    // update a movie in the database
    public function update(MovieUpdateRequest $request, Movie $movie)
    {
        $movie->fill($request->movie);
        $movie->save();
        
        $this->onUpdateMovieCasts($request,$movie);
        $this->onUpdateMovieVideo($request,$movie);
        $this->onUpdateMovieGenres($request,$movie);
        $this->onUpdateMovieSubstitles($request,$movie);
        $this->onUpdateMovieDownloads($request,$movie);
        $this->onUpdateMovieNetwork($request,$movie);

        $data = ['status' => 200, 'message' => 'successfully updated', 'body' => "Success"];

        return response()->json($data, $data['status']);
    }

    public function onUpdateMovieNetwork($request,$movie) {

        if ($request->movie['networks']) {
            foreach ($request->movie['networks'] as $netwok) {
                if (!isset($netwok['network_id'])) {
                    $find = Network::find($netwok['id']) ?? new Network();
                    $find->fill($netwok);
                    $find->save();
                    $movieNetwork = MovieNetwork::where('movie_id', $movie->id)->where('network_id', $netwok['id'])->get();
                    if (count($movieNetwork) < 1) {
                        $movieNetwork = new MovieNetwork();
                        $movieNetwork->network_id = $netwok['id'];
                        $movieNetwork->movie_id = $movie->id;
                        $movieNetwork->save();
                    }
                }
            }
        }

    }
    

    public function onUpdateMovieDownloads($request,$movie) {

        if ($request->linksDownloads) {
            foreach ($request->linksDownloads as $link) {
                if (!isset($link['id'])) {
                    $movieVideo = new MovieDownload();
                    $movieVideo->movie_id = $movie->id;
                    $movieVideo->fill($link);
                    $movieVideo->save();
                }
            }
        }

    }

    public function onUpdateMovieVideo($request,$movie) {

        if ($request->links) {
            foreach ($request->links as $link) {
                if (!isset($link['id'])) {
                    $movieVideo = new MovieVideo();
                    $movieVideo->movie_id = $movie->id;
                    $movieVideo->fill($link);
                    $movieVideo->save();
                }
            }
        }

    }


    public function onUpdateMovieCasts($request,$movie){

        if ($request->movie['casterslist']) {
            foreach ($request->movie['casterslist'] as $genre) {
                
                    $find = Cast::find($genre['id'] ?? 0) ?? new Cast();
                    $find->fill($genre);
                    $find->save();
                    $movieGenre = MovieCast::where('movie_id', $movie->id)
                        ->where('cast_id', $genre['id'])->get();

                    if (count($movieGenre) < 1) {
                        $movieGenre = new MovieCast();
                        $movieGenre->cast_id = $genre['id'];
                        $movieGenre->movie_id = $movie->id;
                        $movieGenre->save();
                
                    }
                
            }
        }

}

    public function onUpdateMovieGenres($request,$movie){

        if ($request->movie['genres']) {
            foreach ($request->movie['genres'] as $genre) {
                if (!isset($genre['genre_id'])) {
                    $find = Genre::find($genre['id'] ?? 0) ?? new Genre();
                    $find->fill($genre);
                    $find->save();
                    $movieGenre = MovieGenre::where('movie_id', $movie->id)
                        ->where('genre_id', $genre['id'])->get();
                    if (count($movieGenre) < 1) {
                        $movieGenre = new MovieGenre();
                        $movieGenre->genre_id = $genre['id'];
                        $movieGenre->movie_id = $movie->id;
                        $movieGenre->save();
                    }
                }
            }
        }

    }


    public function onUpdateMovieSubstitles($request,$movie) {

        if ($request->linksubs) {
            foreach ($request->linksubs as $substitle) {
                if (!isset($substitle['id'])) {
                
                    $movieVideo = new MovieSubstitle();
                    $movieVideo->movie_id = $movie->id;
                    $movieVideo->fill($substitle);
                    $movieVideo->save();
                }
            }
        }

    }

    // delete a movie in the database
    public function destroy(Movie $movie)
    {
        if ($movie != null) {
            $movie->delete();

            $data = ['status' => 200, 'message' => 'successfully removed',];
        } else {
            $data = ['status' => 400, 'message' => 'could not be deleted',];
        }

        return response()->json($data, $data['status']);
    }

    // remove the genre of a movie from the database
    public function destroyGenre($genre)
    {

        if ($genre != null) {

            MovieGenre::find($genre)->delete();

            $data = ['status' => 200, 'message' => 'successfully deleted',];
        } else {
            $data = ['status' => 400, 'message' => 'could not be deleted',];
        }

        return response()->json($data, 200);

    }



     // remove Network from  a movie
     public function destroyNetworks($id)
     {
 
         if ($id != null) {
 
             MovieNetwork::find($id)->delete();
             $data = ['status' => 200, 'message' => 'successfully deleted',];
         } else {
             $data = [
                 'status' => 400,
                 'message' => 'could not be deleted',
             ];
         }
 
         return response()->json($data, $data['status']);
 
     }


    // remove the cast of a movie from the database
    public function destroyCast($id)
    {

        if ($id != null) {

            $movie = MovieCast::where('cast_id', '=', $id)->first();
            $movie->delete();
            $data = ['status' => 200, 'message' => 'successfully deleted',];
        } else {
            $data = [
                'status' => 400,
                'message' => 'could not be deleted',
            ];
        }

        return response()->json($data, $data['status']);

    }

    // save a new image in the movies folder of the storage
    public function storeImg(StoreImageRequest $request)
    {
        if ($request->hasFile('image')) {
            $filename = Storage::disk('movies')->put('', $request->image);
            $data = ['status' => 200, 'image_path' => $request->root() . '/api/movies/image/' . $filename, 'message' => 'successfully uploaded'];
        } else {
            $data = ['status' => 400, 'message' => 'could not be uploaded'];
        }

        return response()->json($data, $data['status']);
    }

    // return an image from the movies folder of the storage
    public function getImg($filename)
    {

        $image = Storage::disk('movies')->get($filename);

        $mime = Storage::disk('movies')->mimeType($filename);

        return (new Response($image, 200))->header('Content-Type', $mime);
    }

    

    // remove a video from a movie from the database
    public function videoDestroy($video)
    {
        if ($video != null) {

            MovieVideo::find($video)->delete();


            $videoMovie = MovieVideo::find($video)->video_name;


            if ($videoMovie != null) {

                Storage::disk('videos')->delete($videoMovie);
                
            }
          

            $data = ['status' => 200, 'message' => 'successfully deleted',];
        } else {
            $data = ['status' => 400, 'message' => 'could not be deleted',];
        }

        return response()->json($data, 200);
    }



    public function downloadDestroy($download)
    {
        if ($download != null) {

            MovieDownload::find($download)->delete();

            $data = ['status' => 200, 'message' => 'successfully deleted',];
        } else {
            $data = ['status' => 400, 'message' => 'could not be deleted',];
        }

        return response()->json($data, 200);
    }


    public function substitleDestroy($substitle)
    {
        if ($substitle != null) {

            MovieSubstitle::find($substitle)->delete();

            $data = ['status' => 200, 'message' => 'successfully deleted',];
        } else {
            $data = ['status' => 400, 'message' => 'could not be deleted',];
        }

        return response()->json($data, 200);
    }




    public function homecontent()
    {

        // Latest

        $movies = Movie::with(['genres','substitles'])
        ->select('movies.title','movies.id','movies.poster_path','movies.backdrop_path','movies.vote_average','movies.subtitle')
        ->where('created_at', '>', Carbon::now()->subMonth())
        ->where('active', '=', 1)
        ->orderByDesc('created_at')
        ->addSelect(DB::raw("'movie' as type"))
        ->limit(10)
        ->get();

        $series = Serie::select('series.name','series.id','series.poster_path','series.backdrop_path',
        'series.vote_average','series.subtitle')
        ->where('created_at', '>', Carbon::now()->subMonth())
        ->where('active', '=', 1)
        ->orderByDesc('created_at')
        ->addSelect(DB::raw("'serie' as type"))
        ->limit(10)
        ->get();

        $animes = Anime::select('animes.name','animes.id','animes.poster_path',
        'animes.backdrop_path','animes.vote_average','animes.subtitle')
        ->where('created_at', '>', Carbon::now()->subMonth())
        ->where('active', '=', 1)
        ->orderByDesc('created_at')
        ->addSelect(DB::raw("'anime' as type"))
        ->limit(10)
        ->get();

        $latest = array_merge($movies->makeHidden(['videos','casters','casterslist','networkslist','networks'])
        ->toArray(), $series->makeHidden(['videos','seasons','casters','casterslist','networkslist','networks'])->toArray(),
        $animes->makeHidden(['videos','seasons','casters','casterslist','networkslist','networks'])->toArray());



        $movieschoosed = 
        Movie::select('movies.title','movies.id','movies.poster_path','movies.vote_average','movies.subtitle')
        ->addSelect(DB::raw("'movie' as type"))->inRandomOrder()
        ->where('active', '=', 1)->limit(4)->get();

        $serieschoosed = Serie::select('series.name','series.id','series.poster_path','series.vote_average','series.subtitle')
        ->addSelect(DB::raw("'serie' as type"))
        ->inRandomOrder()->where('active', '=', 1)->limit(4)->get();

        $animeschoosed = Anime::select('animes.name','animes.id','animes.poster_path',
        'animes.vote_average','animes.subtitle')
        ->addSelect(DB::raw("'anime' as type"))
        ->inRandomOrder()->where('active', '=', 1)->limit(4)->get();


        
        $arraychoosed = array_merge($movieschoosed->makeHidden(['casterslist','casters'
        ,'videos','networks','substitles','downloads','networkslist','networks'])->toArray(),
        $serieschoosed->makeHidden(['casterslist','casters','seasons','networks','substitles',
        'downloads','networkslist'])->toArray(),
        $animeschoosed->makeHidden(['casterslist','casters','seasons','networks','substitles',
        'downloads','networkslist'])->toArray());





        $moviesrecommended = Movie::select('movies.title','movies.id','movies.poster_path','movies.vote_average','movies.subtitle','movies.backdrop_path')
        ->addSelect(DB::raw("'movie' as type"))
        ->orderByDesc('vote_average')->where('active', '=', 1)->limit(10)->get();

        $seriesrecommended = Serie::select('series.name','series.id','series.poster_path','series.vote_average','series.backdrop_path','series.subtitle')
        ->addSelect(DB::raw("'serie' as type"))
        ->orderByDesc('vote_average')->where('active', '=', 1)->limit(10)->get();

        $animesrecommended = Anime::select('animes.name','animes.id','animes.poster_path','animes.backdrop_path'
        ,'animes.vote_average','animes.subtitle','animes.is_anime','animes.subtitle')
        ->addSelect(DB::raw("'anime' as type"))
        ->orderByDesc('vote_average')->where('active', '=', 1)->limit(10)->get();


        $arrayrecommended = array_merge($moviesrecommended->makeHidden(['videos','casterslist','casters',
        'networkslist','downloads','substitles','networks'])
        ->toArray(), $seriesrecommended->makeHidden(['seasons','casterslist','casters',
        'networkslist','downloads','substitles','networks'])->toArray()
        ,$animesrecommended->makeHidden(['seasons','casterslist','casters',
        'networkslist','downloads','substitles','networks'])->toArray());



        $moviesthisweek = Movie::select('movies.title','movies.id','movies.poster_path','movies.vote_average','movies.subtitle')
        ->addSelect(DB::raw("'movie' as type"))
        ->where('created_at', '>', Carbon::now()->startOfWeek())
        ->where('active', '=', 1)
        ->orderByDesc('created_at')->limit(10)->get();

        $seriesthisweek = Serie::select('series.name','series.id','series.poster_path','series.vote_average','series.subtitle')
        ->addSelect(DB::raw("'serie' as type"))
        ->where('created_at', '>', Carbon::now()->startOfWeek())
        ->where('active', '=', 1)
        ->orderByDesc('created_at')->limit(10)->get();

        $animesthisweek = Anime::select('animes.name','animes.id','animes.poster_path','animes.backdrop_path'
        ,'animes.vote_average','animes.subtitle','animes.is_anime','animes.subtitle')
        ->addSelect(DB::raw("'anime' as type"))
        ->where('created_at', '>', Carbon::now()->startOfWeek())
        ->where('active', '=', 1)
        ->orderByDesc('created_at')->limit(10)->get();

        $arraythisweek = array_merge($moviesthisweek->makeHidden(['videos','casterslist','casters',
        'networkslist','networks','downloads','substitles'])
        ->toArray(), $seriesthisweek->makeHidden(['seasons','casterslist','casters',
        'networkslist','networks','downloads','substitles'])->toArray(),
        $animesthisweek->makeHidden(['seasons','casterslist','casters','networkslist'
        ,'networks','downloads','substitles'])->toArray());



        $moviestrending = Movie::select('movies.title','movies.id','movies.poster_path','movies.vote_average','movies.subtitle')
        ->addSelect(DB::raw("'movie' as type"))
        ->where('active', '=', 1)->orderByDesc('views')->limit(10)->get();

        $seriestrending = Serie::select('series.name','series.id','series.poster_path','series.vote_average','series.subtitle')
        ->addSelect(DB::raw("'serie' as type"))
        ->where('active', '=', 1)->orderByDesc('views')->limit(10)->get();

        $animestrending = Anime::select('animes.name','animes.id','animes.poster_path','animes.backdrop_path'
        ,'animes.vote_average','animes.subtitle','animes.subtitle')
        ->addSelect(DB::raw("'anime' as type"))
        ->where('active', '=', 1)->orderByDesc('views')->limit(10)->get();

        $arraytrending = array_merge($moviestrending->makeHidden(['videos','casterslist','casters',
        'networkslist','networks','downloads','substitles'])
        ->toArray(), $seriestrending->makeHidden(['seasons','casterslist','casters'
        ,'networkslist','networks'])->toArray(),
        $animestrending->makeHidden(['seasons','casterslist','casters'
        ,'networkslist','networks'])->toArray());



        $moviespinned = Movie::select('movies.id','movies.title','movies.poster_path','movies.vote_average','movies.pinned')
        ->addSelect(DB::raw("'movie' as type"))->where('pinned', 1)->where('active', '=', 1)
        ->orderByDesc('created_at')->limit(10)->get();

        $seriespinned = Serie::select('series.id','series.name','series.poster_path','series.vote_average','series.pinned')
        ->addSelect(DB::raw("'serie' as type"))->where('pinned', 1)->where('active', '=', 1)
        ->orderByDesc('created_at')->limit(10)->get();


        $animespinned = Anime::select('animes.id','animes.name','animes.poster_path','animes.vote_average','animes.pinned')
        ->addSelect(DB::raw("'anime' as type"))->where('pinned', 1)->where('active', '=', 1)
        ->orderByDesc('created_at')->limit(10)->get();


        $arraypinned = array_merge($moviespinned->makeHidden(['videos','casterslist'
        ,'casters','genres','genreslist','substitles','networkslist','networks'])
        ->toArray(), $seriespinned->makeHidden(['seasons','casterslist',
        'casters','genres','genreslist','substitles','networkslist','networks'])->toArray()
        ,$animespinned->makeHidden(['seasons','casterslist','casters',
        'genres','genreslist','substitles','networkslist','networks'])->toArray());


        $moviestop10 = Movie::select('movies.id','movies.title','movies.poster_path',
        'movies.vote_average','movies.pinned')
        ->addSelect(DB::raw("'movie' as type"))
        ->where('active', '=', 1)
        ->orderByDesc('views')->limit(5)->get();

        $seriestop10 = Serie::select('series.id','series.name','series.poster_path',
        'series.vote_average','series.pinned')
        ->addSelect(DB::raw("'serie' as type"))
        ->where('active', '=', 1)
        ->orderByDesc('views')->limit(5)->get();


        
        $animestop10 = Anime::select('animes.id','animes.name','animes.poster_path',
        'animes.vote_average','animes.pinned')
        ->addSelect(DB::raw("'anime' as type"))
        ->where('active', '=', 1)
        ->orderByDesc('views')->limit(5)->get();

        $arraytop10 = array_merge($moviestop10->makeHidden(['videos','casterslist'
        ,'casters','genres','genreslist','substitles','networkslist','networks'])
        ->toArray(), $seriestop10->makeHidden(['seasons','casterslist','casters',
        'genres','genreslist','substitles','networkslist','networks'])->toArray());


        $moviespopular = Movie::select('movies.title','movies.id','movies.poster_path'
        ,'movies.vote_average','movies.subtitle')->where('active', '=', 1)
        ->orderByDesc('popularity')->limit(10)->get()
        ->makeHidden(['videos','casterslist','casters','networkslist','networks','downloads','substitles']);


        $moviesrecents = Serie::select('series.name','series.id','series.poster_path',
        'series.vote_average','series.subtitle')
        ->where('created_at', '>', Carbon::now()->subMonth(3))
        ->where('active', '=', 1)
       ->orderByDesc('created_at')
       ->limit(10)
       ->get()->makeHidden(['seasons','casterslist','casters','substitles','networkslist','networks'])->toArray();


       $casts = Cast::select('casts.id','casts.name','casts.profile_path','casts.gender')
       ->where('active', '=', 1)
       ->orderByDesc('views')
       ->limit(15)
       ->get();



       $settings = Setting::first();

       $featured = Featured::orderBy('position')->orderByDesc('updated_at')
           ->limit($settings->featured_home_numbers)
           ->get();



      $animeslatest = Anime::select('animes.id','animes.name','animes.poster_path','animes.vote_average',
    'animes.is_anime','animes.vote_average','animes.newEpisodes','animes.subtitle')
    ->where('created_at', '>', Carbon::now()->subMonth(3))
        ->where('active', '=', 1)
        ->orderByDesc('created_at')
        ->limit(10)->get()->makeHidden(['casterslist','casters','seasons',
        'overview','backdrop_path','preview_path','videos'
        ,'substitles','vote_count','popularity','runtime','release_date',
        'imdb_external_id','hd','pinned','preview','networkslist','networks']);



        $order = 'desc';
        $serieslatest_episodes = Serie::where('active', '=', 1)->join('seasons', 'seasons.serie_id', '=', 'series.id')
        ->join('episodes', 'episodes.season_id', '=', 'seasons.id')
        ->join('serie_videos', 'serie_videos.episode_id', '=', 'episodes.id')
        ->orderBy('serie_videos.updated_at', $order)->select('serie_videos.episode_id','series.id','series.tmdb_id as serieTmdb'
        ,'series.name','episodes.still_path','episodes.season_id','episodes.name as episode_name','serie_videos.link','serie_videos.server','serie_videos.lang'
        ,'serie_videos.embed','serie_videos.youtubelink','serie_videos.hls','seasons.name as seasons_name','seasons.season_number','episodes.vote_average'
        ,'series.premuim','episodes.episode_number','series.poster_path','episodes.hasrecap','episodes.skiprecap_start_in'
        ,'serie_videos.supported_hosts','serie_videos.header','serie_videos.useragent','series.imdb_external_id'
        ,'serie_videos.drmuuid','serie_videos.drmlicenceuri','serie_videos.drm'
        )->limit(10)->get()->unique('episode_id')->makeHidden('seasons','episodes');

        $newEpisodes = [];
        foreach ($serieslatest_episodes->makeHidden(['seasons','casterslist','casters','networkslist','networks'])->toArray() as $item) {
            array_push($newEpisodes, $item);
        }



        $animeslatest_episodes = Anime::where('active', '=', 1)->join('anime_seasons', 'anime_seasons.anime_id', '=', 'animes.id')
        ->join('anime_episodes', 'anime_episodes.anime_season_id', '=', 'anime_seasons.id')
        ->join('anime_videos', 'anime_videos.anime_episode_id', '=', 'anime_episodes.id')
        ->orderBy('anime_videos.updated_at', $order)->orderBy('anime_videos.anime_episode_id', $order)->select('anime_videos.anime_episode_id','animes.id'
        ,'animes.name','anime_episodes.still_path','anime_episodes.anime_season_id','anime_episodes.name as episode_name','anime_videos.link','anime_videos.server','anime_videos.lang'
        ,'anime_videos.embed','anime_videos.youtubelink','anime_videos.hls','anime_seasons.name as seasons_name','anime_seasons.season_number','anime_episodes.vote_average'
        ,'animes.premuim','animes.tmdb_id','anime_episodes.episode_number','animes.poster_path',
        'anime_episodes.hasrecap',
        'anime_episodes.skiprecap_start_in','anime_videos.supported_hosts'
        ,'anime_videos.drmuuid','anime_videos.drmlicenceuri','anime_videos.drm'
        )->addSelect(DB::raw("'anime' as type"))
        ->limit(10)->get()->unique('anime_episode_id')->makeHidden(['seasons','episodes','casterslist','networkslist','networks','casters']);
    
    
        $newEpisodesanimes = [];
        foreach ($animeslatest_episodes as $item) {
            array_push($newEpisodesanimes, $item);
        }
    


        $moviespopularSeries = Serie::select('series.id','series.name','series.poster_path','series.vote_average'
        ,'series.subtitle')->where('active', '=', 1)
        ->orderByDesc('popularity')
        ->limit(10)->get()->makeHidden(['casterslist','casters','seasons','overview','backdrop_path','preview_path','videos'
        ,'substitles','vote_count','popularity','runtime','release_date','imdb_external_id','hd','pinned','preview'
        ,'networkslist','networks']);


        $streaming = Livetv::select('livetvs.id','livetvs.name','livetvs.poster_path')
        ->orderByDesc('created_at')->where('active', '=', 1)
        ->limit(10)
        ->get();

    
      return response()
      ->json(['latest' => $latest,
      'choosed' => $arraychoosed,
      'recommended' => $arrayrecommended,
      'thisweek' => $arraythisweek,
      'trending' => $arraytrending,
      'pinned' => $arraypinned,
      'top10' => $arraytop10,
      'popular' => $moviespopular,
      'recents' => $moviesrecents,
       'popular_casters' => $casts,
       'featured' => $featured,
       'anime' => $animeslatest,
       'latest_episodes' => $newEpisodes,
       'latest_episodes_animes' => $newEpisodesanimes,
       'popularSeries' => $moviespopularSeries,
       'livetv' => $streaming], 200);


    }



    // returns 15 movies with a release date of less than 6 months
    public function latestcontent()
    {

        $movies = Movie::with(['genres','substitles'])
        ->select('movies.title','movies.id','movies.poster_path','movies.backdrop_path','movies.vote_average','movies.subtitle')
        ->where('created_at', '>', Carbon::now()->subMonth())
        ->where('active', '=', 1)
        ->orderByDesc('created_at')
        ->addSelect(DB::raw("'movie' as type"))
        ->limit(10)
        ->get();

        $series = Serie::select('series.name','series.id','series.poster_path','series.backdrop_path',
        'series.overview','series.vote_average','series.subtitle')
        ->where('created_at', '>', Carbon::now()->subMonth())
        ->where('active', '=', 1)
        ->orderByDesc('created_at')
        ->addSelect(DB::raw("'serie' as type"))
        ->limit(10)
        ->get();

        $animes = Anime::select('animes.name','animes.id','animes.poster_path','animes.backdrop_path',
        'animes.overview','animes.vote_average','animes.subtitle')
        ->where('created_at', '>', Carbon::now()->subMonth())
        ->where('active', '=', 1)
        ->orderByDesc('created_at')
        ->addSelect(DB::raw("'anime' as type"))
        ->limit(10)
        ->get();

        $array = array_merge($movies->makeHidden(['videos','casters','casterslist'])
        ->toArray(), $series->makeHidden(['videos','seasons','casters','casterslist'])->toArray(),
        $animes->makeHidden(['videos','seasons','casters','casterslist'])->toArray());


        return response()
            ->json(['latest' => $array], 200);
         

    }



    public function playSomething()
    {


     $order = 'desc';
      $series = Movie::with(['genres','videos'])->where('active', '=', 1)->whereHas('videos', function ($query) use ($project) {
      $query->whereNotNull('link');
     })->inRandomOrder()->limit(1)->first();

     return response()->json($series, 200);
       
    }



    public function choosedcontent()
    {

        $movies = Movie::select('movies.title','movies.id','movies.poster_path','movies.vote_average','movies.subtitle')
        ->addSelect(DB::raw("'movie' as type"))->inRandomOrder()
        ->where('active', '=', 1)->limit(6)->get();

        $series = Serie::select('series.name','series.id','series.poster_path','series.vote_average','series.subtitle')
        ->addSelect(DB::raw("'serie' as type"))
        ->inRandomOrder()->where('active', '=', 1)->limit(6)->get();

        $animes = Anime::select('animes.name','animes.id','animes.poster_path','animes.backdrop_path',
        'animes.overview','animes.vote_average','animes.subtitle','animes.is_anime','animes.subtitle')
        ->addSelect(DB::raw("'anime' as type"))
        ->inRandomOrder()->where('active', '=', 1)->limit(6)->get();

        $array = array_merge($movies->makeHidden(['casterslist','casters','videos','networks','substitles','downloads','networkslist'])->toArray(),
         $series->makeHidden(['casterslist','casters','seasons','networks','substitles','downloads','networkslist'])->toArray(),
         $animes->makeHidden(['casterslist','casters','seasons','networks','substitles','downloads','networkslist'])->toArray());


        return response()->json(['choosed' => $array], 200);
    }


    // return the 10 movies with the highest average votes
    public function recommendedcontent()
    {


        $movies = Movie::select('movies.title','movies.id','movies.poster_path','movies.vote_average','movies.subtitle','movies.backdrop_path')
        ->addSelect(DB::raw("'movie' as type"))
        ->orderByDesc('vote_average')->where('active', '=', 1)->limit(10)->get();

        $series = Serie::select('series.name','series.id','series.poster_path','series.vote_average','series.backdrop_path','series.subtitle')
        ->addSelect(DB::raw("'serie' as type"))
        ->orderByDesc('vote_average')->where('active', '=', 1)->limit(10)->get();

        $animes = Anime::select('animes.name','animes.id','animes.poster_path','animes.backdrop_path'
        ,'animes.vote_average','animes.subtitle','animes.is_anime','animes.subtitle')
        ->addSelect(DB::raw("'anime' as type"))
        ->orderByDesc('vote_average')->where('active', '=', 1)->limit(10)->get();


        $array = array_merge($movies->makeHidden(['videos','casterslist','casters','networkslist','downloads','substitles','networks'])
        ->toArray(), $series->makeHidden(['seasons','casterslist','casters','networkslist','downloads','substitles','networks'])->toArray()
        ,$animes->makeHidden(['seasons','casterslist','casters','networkslist','downloads','substitles','networks'])->toArray());


        return response()->json(['recommended' => $array], 200);
    }

    public function thisweekcontent()
    {

 
        $movies = Movie::select('movies.title','movies.id','movies.poster_path','movies.vote_average','movies.subtitle')
        ->addSelect(DB::raw("'movie' as type"))
        ->where('created_at', '>', Carbon::now()->startOfWeek())
        ->where('active', '=', 1)
        ->orderByDesc('created_at')->limit(10)->get();

        $series = Serie::select('series.name','series.id','series.poster_path','series.vote_average','series.subtitle')
        ->addSelect(DB::raw("'serie' as type"))
        ->where('created_at', '>', Carbon::now()->startOfWeek())
        ->where('active', '=', 1)
        ->orderByDesc('created_at')->limit(10)->get();

        $animes = Anime::select('animes.name','animes.id','animes.poster_path','animes.backdrop_path'
        ,'animes.vote_average','animes.subtitle','animes.is_anime','animes.subtitle')
        ->addSelect(DB::raw("'anime' as type"))
        ->where('created_at', '>', Carbon::now()->startOfWeek())
        ->where('active', '=', 1)
        ->orderByDesc('created_at')->limit(10)->get();

        $array = array_merge($movies->makeHidden(['videos','casterslist','casters','networkslist','networks','downloads','substitles'])
        ->toArray(), $series->makeHidden(['seasons','casterslist','casters','networkslist','networks','downloads','substitles'])->toArray(),
        $animes->makeHidden(['seasons','casterslist','casters','networkslist','networks','downloads','substitles'])->toArray());

        return response()
            ->json(['thisweek' => $array], 200);
    }

    public function trendingcontent()
    {

        $movies = Movie::select('movies.title','movies.id','movies.poster_path','movies.vote_average','movies.subtitle')
        ->addSelect(DB::raw("'movie' as type"))
        ->where('active', '=', 1)->orderByDesc('views')->limit(10)->get();

        $series = Serie::select('series.name','series.id','series.poster_path','series.vote_average','series.subtitle')
        ->addSelect(DB::raw("'serie' as type"))
        ->where('active', '=', 1)->orderByDesc('views')->limit(10)->get();

        $animes = Anime::select('animes.name','animes.id','animes.poster_path','animes.backdrop_path'
        ,'animes.vote_average','animes.subtitle','animes.subtitle')
        ->addSelect(DB::raw("'anime' as type"))
        ->where('active', '=', 1)->orderByDesc('views')->limit(10)->get();

        $array = array_merge($movies->makeHidden(['videos','casterslist','casters','networkslist','networks','downloads','substitles'])
        ->toArray(), $series->makeHidden(['seasons','casterslist','casters','networkslist','networks'])->toArray(),
        $animes->makeHidden(['seasons','casterslist','casters','networkslist','networks'])->toArray());

        return response()
            ->json(['trending' => $array], 200);
    }

    public function featuredcontent()  {



        $movies = Movie::select('movies.title','movies.id','movies.poster_path','movies.vote_average')->addSelect(DB::raw("'movie' as type"))->where('featured', 1)
            ->where('active', '=', 1)
            ->orderByDesc('created_at')
            ->limit(8)->get()->makeHidden(['videos','casterslist','casters','commentslist']);

        $series = Serie::select('series.id','series.name','series.poster_path','series.vote_average')->addSelect(DB::raw("'serie' as type"))->where('featured', 1)->where('active', '=', 1)
            ->orderByDesc('created_at')
            ->limit(8)->get()->makeHidden(['seasons','episodes','overview','casterslist','casters','commentslist']);


        $animes = Anime::select('animes.id','animes.name','animes.poster_path','animes.vote_average','animes.is_anime')->addSelect(DB::raw("'anime' as type"))->where('featured', 1)->where('active', '=', 1)
            ->orderByDesc('created_at')
            ->limit(4)->get()->makeHidden(['seasons','episodes','overview','casterslist','casters','commentslist']);
    
        $array = array_merge($movies->toArray(), $series->toArray(),$animes->toArray());



        return response()
            ->json(['featured' => usort($array, "updated_at")], 200);

    }



    public function pinnedcontent()

    {


        $movies = Movie::select('movies.id','movies.title','movies.poster_path','movies.vote_average','movies.pinned')
        ->addSelect(DB::raw("'movie' as type"))->where('pinned', 1)->where('active', '=', 1)
        ->orderByDesc('created_at')->limit(10)->get();

        $series = Serie::select('series.id','series.name','series.poster_path','series.vote_average','series.pinned')
        ->addSelect(DB::raw("'serie' as type"))->where('pinned', 1)->where('active', '=', 1)
        ->orderByDesc('created_at')->limit(10)->get();


        $animes = Anime::select('animes.id','animes.name','animes.poster_path','animes.vote_average','animes.pinned')
        ->addSelect(DB::raw("'anime' as type"))->where('pinned', 1)->where('active', '=', 1)
        ->orderByDesc('created_at')->limit(10)->get();


        $array = array_merge($movies->makeHidden(['videos','casterslist','casters','genres','genreslist','substitles'])
        ->toArray(), $series->makeHidden(['seasons','casterslist','casters','genres','genreslist','substitles'])->toArray()
        ,$animes->makeHidden(['seasons','casterslist','casters','genres','genreslist','substitles'])->toArray());

        return response()
            ->json(['pinned' => $array], 200);

    }



    public function topcontent()

    {

        $movies = Movie::select('movies.id','movies.title','movies.poster_path',
        'movies.vote_average','movies.pinned')
        ->addSelect(DB::raw("'movie' as type"))
        ->where('active', '=', 1)
        ->orderByDesc('views')->limit(5)->get();

        $series = Serie::select('series.id','series.name','series.poster_path',
        'series.vote_average','series.pinned')
        ->addSelect(DB::raw("'serie' as type"))
        ->where('active', '=', 1)
        ->orderByDesc('views')->limit(5)->get();


        
        $animes = Anime::select('animes.id','animes.name','animes.poster_path',
        'animes.vote_average','animes.pinned')
        ->addSelect(DB::raw("'anime' as type"))
        ->where('active', '=', 1)
        ->orderByDesc('views')->limit(5)->get();

        $array = array_merge($movies->makeHidden(['videos','casterslist','casters','genres','genreslist','substitles'])
        ->toArray(), $series->makeHidden(['seasons','casterslist','casters','genres','genreslist','substitles'])->toArray());


        return response()
            ->json(['top10' => $array], 200);

    }




    public function randomcontent($statusapi)

    {
        


        $movies = Movie::inRandomOrder() ->where('active', '=', 1)->limit(1)->get();
 

        return response()
            ->json(['random' => $movies], 200);

    }

    public function randomMovie()

    {
        
        $movies = Movie::inRandomOrder() ->where('active', '=', 1)->limit(1)->first();
 
        return response()
            ->json($movies, 200);
    }

    public function suggestedcontent($statusapi)

    {

        $movies = Movie::select('movies.title','movies.id','movies.poster_path',
        'movies.backdrop_path','movies.overview','movies.vote_average','movies.subtitle')
        ->take(3)->where('active', '=', 1)->where('active', '=', 1)
        ->addSelect(DB::raw("'Movie' as type"))->get()->makeHidden(['videos','casterslist','casters','networkslist'
    ,'downloads','networks','substitles']);

        $series = Serie::select('series.id','series.name','series.poster_path',
        'series.backdrop_path','series.vote_average','series.overview','series.subtitle')
        ->take(3)->where('active', '=', 1)->addSelect(DB::raw("'Serie' as type"))->get()->makeHidden(['seasons','casterslist','casters'
        ,'networkslist','downloads','networks','substitles']);

        $livetv = LiveTV::select('livetvs.id','livetvs.name','livetvs.poster_path','livetvs.backdrop_path','livetvs.overview')
        ->take(3)->where('active', '=', 1)->addSelect(DB::raw("'Streaming' as type"))->get();

        $animes = Anime::select('animes.id','animes.name','animes.poster_path','animes.backdrop_path'
        ,'animes.vote_average','animes.is_anime'
        ,'animes.newEpisodes','animes.overview','animes.subtitle')
        ->take(3)->where('active', '=', 1)->addSelect(DB::raw("'Anime' as type"))->get()->makeHidden(['seasons','casterslist','casters','networkslist'
        ,'downloads','networks','substitles']);

    
        $array = array_merge($movies->toArray(), $series->toArray(),$livetv->toArray() ,$animes->toArray());

        return response()
            ->json(['suggested' => $array], 200);

    }

    // return the 10 movies with the most popularity
    public function popularcontent($statusapi)
    {


        $movies = Movie::select('movies.title','movies.id','movies.poster_path','movies.vote_average','movies.subtitle')->where('active', '=', 1)->orderByDesc('popularity')->limit(10)->get()
        ->makeHidden(['videos','casterslist','casters']);

        return response()
            ->json(['popular' => $movies], 200);
    }

    // returns the last 10 movies added in the month
    public function recentscontent($statusapi)
    {


        $movies = Movie::select('movies.title','movies.id','movies.poster_path','movies.vote_average','movies.subtitle')->where('created_at', '>', Carbon::today()->addDays(14))
        ->where('active', '=', 1)
       ->orderByDesc('created_at')
       ->limit(10)
       ->get();



        return response()
            ->json(['recents' => $movies->makeHidden(['videos','casterslist','casters'])
            ->toArray()], 200);
    }

    public function recentsthisweek($statusapi)
    {


        $movies = Movie::select('movies.title','movies.id','movies.poster_path','movies.vote_average','movies.subtitle')->where('created_at', '>', Carbon::now()->startOfWeek())
        ->where('active', '=', 1)
        ->orderByDesc('created_at')->limit(10)->get();

        $series = Serie::select('series.name','series.id','series.poster_path','series.vote_average','series.subtitle')->where('created_at', '>', Carbon::now()->startOfWeek())
        ->where('active', '=', 1)
        ->orderByDesc('created_at')->limit(10)->get();


        $array = array_merge($movies->makeHidden(['videos','casterslist','casters'])
        ->toArray(), $series->makeHidden(['seasons','casterslist','casters'])->toArray());


        return response()
            ->json(['recentsthisweek' => $array], 200);
    }

    // returns 12 movies related to a movie
    public function relateds(Movie $movie)
    {
        $genre = $movie->genres[0]->genre_id;

        $order = 'desc';
      
        $movies = Movie::join('movie_genres', 'movie_genres.movie_id', '=', 'movies.id')
       ->where('genre_id', $genre)
        ->where('movie_id', '!=', $movie->id)
        ->select('movies.title','movies.id','movies.poster_path','movies.vote_average','movies.subtitle')
        ->limit(6)->get()->makeHidden(['videos','casterslist','casters','genres','networkslist','networks','downloads','substitles']);
       
       return response()->json(['relateds' => $movies], 200);
    }

    // return all the videos of a movie
    public function videos(Movie $movie)
    {
        return response()->json($movie->videos, 200);
    }



    // return all the Downlaods of a movie
    public function downloads(Movie $movie)
    {
        return response()->json($movie->downloads, 200);
    }


    public function casters(Movie $movie)
    {
        return response()->json($movie->casterslist, 200);
    }

    // return all the videos of a movie
    public function substitles(Movie $movie)
    {
        return response()->json($movie->substitles, 200);
    }
}

