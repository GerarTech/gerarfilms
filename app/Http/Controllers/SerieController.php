<?php

namespace App\Http\Controllers;

use App\Embed;
use App\Episode;
use App\Genre;
use App\Network;
use App\Cast;
use App\Http\Requests\SerieStoreRequest;
use App\Http\Requests\SerieUpdateRequest;
use App\Http\Requests\StoreImageRequest;
use App\Jobs\SendNotification;
use App\Season;
use App\Serie;
use App\Anime;
use App\SerieGenre;
use App\SerieVideo;
use App\SerieCast;
use App\SerieNetwork;
use App\SerieSubstitle;
use App\SerieDownload;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class SerieController extends Controller
{


    public function __construct()
    {
        $this->middleware('doNotCacheResponse', ['only' => ['moviecomment','addcomment']]);
    }


    // returns all Series except children Series, for api.
    public function index()
    {
        $serie = Serie::whereDoesntHave('genres', function ($genre) {
            $genre->where('genre_id', '=', 10762);
        })->orderByDesc('id')->paginate(12);

        return response()->json($serie, 200);

    }


 
    public function moviecomment($movie)
    {

        $movie = Serie::where('id', '=', $movie)->first();

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
    
        $movie = Serie::where('id', '=', $request->movie_id)->first();

        $comment = $movie->commentAsUser($user, $request->comments_message);

        return response()->json($comment, 200);

    }


    public function addtofav($id,Request $request)
    {
        
        $serie = Serie::where('id', '=', $id)->first()->addFavorite($request->user()->id);
 
        return response()->json("Success", 200);

    }


    public function removefromfav($id,Request $request)
    {

        $movie = Serie::where('id', '=', $id)->first()->removeFavorite($request->user()->id);

        return response()->json("Added", 200);

    }

    public function isMovieFavorite($id,Request $request)
    {

        $movie = Serie::where('id', '=', $id)->first();

        if($movie->isFavorited($request->user()->id)) {

            $data = ['status' => 200, 'status' => 1,];

        }else {
            
            $data = ['status' => 400, 'status' => 0,];
        }

        return response()->json($data, 200);
    }



    // returns all Series for admin panel
    public function data()
    {


        return response()->json(Serie::with('seasons.episodes.videos','genres','casters','networks')->orderByDesc('created_at')
        ->paginate(12), 200);


    
    }

    // returns a specific Serie
    public function show($serie)
    {

        $serie = Serie::query()
        ->with(['casters.cast' => function ($query) {
            $query->select('id', 'name','original_name','profile_path');
        }])
        ->where('id', '=', $serie)->first()->makeHidden(['casters','networks']);
    
        $serie->increment('views',1);
        
        return response()->json($serie, 200);
    }


    // create a new Serie in the database
    public function store(SerieStoreRequest $request)
    {
        $serie = new Serie();
        $serie->fill($request->serie);
        $serie->save();

        $this->onSaveSerieGenre($request,$serie);
        $this->onSaveSerieSeasons($request,$serie);
        $this->onSaveSerieCasters($request,$serie);
        $this->onSaveSerieNetworks($request,$serie);


        if ($request->notification) {
            $this->dispatch(new SendNotification($serie));
        }

        $data = [
            'status' => 200,
            'message' => 'successfully created',
            'body' => $serie->load('seasons.episodes.videos')
        ];

        return response()->json($data, $data['status']);
    }

    public function onSaveSerieCasters($request,$serie) {

        if ($request->serie['casterslist']) {
            foreach ($request->serie['casterslist'] as $cast) {
                $find = Cast::find($cast['id']);
                if ($find == null) {
                    $find = new Cast();
                    $find->fill($cast);
                    $find->save();
                }
                $movieGenre = new SerieCast();
                $movieGenre->cast_id = $cast['id'];
                $movieGenre->serie_id = $serie->id;
                $movieGenre->save();
            }
        }

    }



    public function onSaveSerieNetworks($request,$serie) {

        if ($request->serie['networks']) {
            foreach ($request->serie['networks'] as $network) {
                $find = Network::find($network['id']);
                if ($find == null) {
                    $find = new Network();
                    $find->fill($network);
                    $find->save();
                }
                $serieNetwork = new SerieNetwork();
                $serieNetwork->network_id = $network['id'];
                $serieNetwork->serie_id = $serie->id;
                $serieNetwork->save();
            }
        }

    }

    public function onSaveSerieGenre($request,$serie) {

        if ($request->serie['genres']) {
            foreach ($request->serie['genres'] as $genre) {
                $find = Genre::find($genre['id']);
                if ($find == null) {
                    $find = new Genre();
                    $find->fill($genre);
                    $find->save();
                }
                $serieGenre = new SerieGenre();
                $serieGenre->genre_id = $genre['id'];
                $serieGenre->serie_id = $serie->id;
                $serieGenre->save();
            }
        }

    }


    public function onSaveSerieSeasons($request , $serie){

        if ($request->serie['seasons']) {
            foreach ($request->serie['seasons'] as $reqSeason) {
                $season = new Season();
                $season->fill($reqSeason);
                $season->serie_id = $serie->id;
                $season->save();
               
                $this->onSaveEpisodes($request,$reqSeason,$season);

                
            }
        }

    }


    public function onSaveEpisodes($request, $reqSeason,$season) {

        if ($reqSeason['episodes']) {
            foreach ($reqSeason['episodes'] as $reqEpisode) {
                $episode = new Episode();
                $episode->fill($reqEpisode);
                $episode->season_id = $season->id;
                $episode->save();


                if (isset($reqEpisode['videos'])) {
                    foreach ($reqEpisode['videos'] as $reqVideo) {
                    
                        $video = SerieVideo::find($reqVideo['id'] ?? 0) ?? new SerieVideo();
                        $video->fill($reqVideo);
                        $video->episode_id = $episode->id;
                        $video->save();
                    }
                }
               

               $this->onSaveEpisodeSubstitle($request,$reqEpisode,$episode);
               $this->onSaveEpisodeDownload($request,$reqEpisode,$episode);
            }
        }


    }



    public function onSaveEpisodeDownload($request,$reqEpisode,$episode) {

        if (isset($reqEpisode['downloads'])) {
            foreach ($reqEpisode['downloads'] as $reqVideo) {
                $video = SerieDownload::find($reqVideo['id'] ?? 0) ?? new SerieDownload();
                $video->fill($reqVideo);
                $video->episode_id = $episode->id;
                $video->save();
            }
        }
    
    }


    public function onSaveEpisodeSubstitle($request,$reqEpisode,$episode) {


         if (isset($reqEpisode['substitles'])) {
                    foreach ($reqEpisode['substitles'] as $reqVideo) {
                        $video = new SerieSubstitle();
                        $video->fill($reqVideo);
                        $video->episode_id = $episode->id;
                        $video->save();
                    }
                }
    }

    // update a Serie in the database
    public function update(SerieUpdateRequest $request, Serie $serie)
    {

        $serie->fill($request->serie);
        $serie->save();

        $this->onUpdateSerieGenre($request,$serie);
        $this->onUpdateSerieSeasons($request,$serie);
        $this->onUpdateSerieCasts($request,$serie);
        $this->onUpdateSerieNetwork($request,$serie);
    
        $data = [
            'status' => 200,
            'message' => 'successfully updated',
            'body' => "Success"
        ];

        return response()->json($data, $data['status']);
    }





    public function onUpdateSerieCasts ($request,$serie) {


        if ($request->serie['casterslist']) {
            foreach ($request->serie['casterslist'] as $genre) {
                
                    $find = Cast::find($genre['id'] ?? 0) ?? new Cast();
                    $find->fill($genre);
                    $find->save();
                    $movieGenre = SerieCast::where('serie_id', $serie->id)
                        ->where('cast_id', $genre['id'])->get();

                    if (count($movieGenre) < 1) {
                        $movieGenre = new SerieCast();
                        $movieGenre->cast_id = $genre['id'];
                        $movieGenre->serie_id = $serie->id;
                        $movieGenre->save();
                
                    }
                
            }
        }

    }



    public function onUpdateSerieNetwork ($request,$serie) {

        if ($request->serie['networks']) {
            foreach ($request->serie['networks'] as $netwok) {
                if (!isset($netwok['network_id'])) {
                    $find = Network::find($netwok['id']) ?? new Network();
                    $find->fill($netwok);
                    $find->save();
                    $serieNetwork = SerieNetwork::where('serie_id', $serie->id)->where('network_id', $netwok['id'])->get();
                    if (count($serieNetwork) < 1) {
                        $serieNetwork = new SerieNetwork();
                        $serieNetwork->network_id = $netwok['id'];
                        $serieNetwork->serie_id = $serie->id;
                        $serieNetwork->save();
                    }
                }
            }
        }

    }

    public function onUpdateSerieGenre ($request,$serie) {

        if ($request->serie['genres']) {
            foreach ($request->serie['genres'] as $genre) {
                if (!isset($genre['genre_id'])) {
                    $find = Genre::find($genre['id']) ?? new Genre();
                    $find->fill($genre);
                    $find->save();
                    $serieGenre = SerieGenre::where('serie_id', $serie->id)->where('genre_id', $genre['id'])->get();
                    if (count($serieGenre) < 1) {
                        $serieGenre = new SerieGenre();
                        $serieGenre->genre_id = $genre['id'];
                        $serieGenre->serie_id = $serie->id;
                        $serieGenre->save();
                    }
                }
            }
        }

    }


    public function onUpdateSerieSeasons($request,$serie) {


        if ($request->serie['seasons']) {
            foreach ($request->serie['seasons'] as $reqSeason) {
                $season = Season::find($reqSeason['id'] ?? 0) ?? new Season();
                $season->fill($reqSeason);
                $season->serie_id = $serie->id;
                $season->save();


                $this->onUpdateSerieEpisodes($request,$reqSeason,$season);
            }
        }


    }




    public function onUpdateSerieEpisodes ($request,$reqSeason,$season) {

        if ($reqSeason['episodes']) {
                    foreach ($reqSeason['episodes'] as $reqEpisode) {
                        $episode = Episode::find($reqEpisode['id'] ?? 0) ?? new Episode();
                        $episode->fill($reqEpisode);
                        $episode->season_id = $season->id;
                        $episode->save();
                        if (isset($reqEpisode['videos'])) {
                            foreach ($reqEpisode['videos'] as $reqVideo) {
                            
                                $video = SerieVideo::find($reqVideo['id'] ?? 0) ?? new SerieVideo();
                                $video->fill($reqVideo);
                                $video->episode_id = $episode->id;
                                $video->save();
                            }
                        }

                
                        $this->onUpdateSerieSubstitle($request,$reqEpisode,$episode);
                        $this->onUpdateSerieDownload($request,$reqEpisode,$episode);
                    }
                }

    }



    public function onUpdateSerieDownload ($request,$reqEpisode,$episode) {

        if (isset($reqEpisode['downloads'])) {
            foreach ($reqEpisode['downloads'] as $reqVideo) {
    
                $substitle = SerieDownload::find($reqVideo['id'] ?? 0) ?? new SerieDownload();
                $substitle->fill($reqVideo);
                $substitle->episode_id = $episode->id;
                $substitle->save();
            }
        
    }
    
    }


    public function onUpdateSerieSubstitle ($request,$reqEpisode,$episode) {

        if (isset($reqEpisode['substitles'])) {
            foreach ($reqEpisode['substitles'] as $reqVideo) {

                $substitle = SerieSubstitle::find($reqVideo['id'] ?? 0) ?? new SerieSubstitle();
                $substitle->fill($reqVideo);
                $substitle->episode_id = $episode->id;
                $substitle->save();
            }
        

    }

}


    // delete a Serie from the database
    public function destroy(Serie $serie)
    {
        if ($serie != null) {
            $serie->delete();

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

    // remove a genre from a Series from the database
    public function destroyGenre($genre)
    {
        if ($genre != null) {

            //SerieGenre::find($genre)->delete();
            $movie = SerieGenre::where('id', '=', $genre)->first();
            $movie->delete();

            $data = ['status' => 200, 'message' => 'successfully deleted',];
        } else {
            $data = ['status' => 400, 'message' => 'could not be deleted',];
        }

        return response()->json($data, 200);
    }


     // remove the cast of a movie from the database
     public function destroyCast($id)
     {
 
         if ($id != null) {
 
             $movie = SerieCast::where('cast_id', '=', $id)->first();
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




// remove Network from  a movie
public function destroyNetworks($id)
{

    if ($id != null) {

        SerieNetwork::find($id)->delete();
        $data = ['status' => 200, 'message' => 'successfully deleted',];
    } else {
        $data = [
            'status' => 400,
            'message' => 'could not be deleted',
        ];
    }

    return response()->json($data, $data['status']);

}



    // save a new image in the Series folder of the storage
    public function storeImg(StoreImageRequest $request)
    {

        if ($request->hasFile('image')) {
            $filename = Storage::disk('series')->put('', $request->image);
            $data = [
                'status' => 200,
                'image_path' => $request->root() . '/api/series/image/' . $filename,
                'message' => 'image uploaded successfully'
            ];
        } else {
            $data = [
                'status' => 400,
                'message' => 'there was an error uploading the image'
            ];
        }

        return response()->json($data, $data['status']);
    }

    // return an image from the Series folder of the storage
    public function getImg($filename)
    {

        $image = Storage::disk('series')->get($filename);

        $mime = Storage::disk('series')->mimeType($filename);

        return (new Response($image, 200))
            ->header('Content-Type', $mime);
    }


    // returns a specific Serie
    public function showbyimdb($serie)
    {

        $movie = Serie::where('tmdb_id', '=', $movie)->orWhere('id', '=', $movie)->first();

        $movie->increment('views',1);
        
        return response()->json($movie, 200);

    }




    // return the 10 Series with the highest average votes
    public function recommended()
    {


        $movies = Serie::select('series.id','series.name','series.poster_path','series.vote_average','series.subtitle')
        ->orderByDesc('vote_average')->where('active', '=', 1)->limit(10)->get();

    return response()->json(['recommended' => 
    $movies->makeHidden(['casterslist','casters','seasons','overview','backdrop_path','preview_path','videos'
    ,'substitles','vote_count','popularity','runtime','release_date','imdb_external_id','hd','pinned','preview'])], 200);


    }

    // return the 10 movies with the most popularity
    public function popular()
    {


        $movies = Serie::select('series.id','series.name','series.poster_path','series.vote_average'
        ,'series.subtitle')->where('active', '=', 1)
        ->orderByDesc('popularity')
        ->limit(10)->get();


    return response()->json(['popularSeries' => $movies->makeHidden(['casterslist','casters','seasons','overview','backdrop_path','preview_path','videos'
    ,'substitles','vote_count','popularity','runtime','release_date','imdb_external_id','hd','pinned','preview'])], 200);


    }

    // returns the last 10 Series added in the month
    public function recents()
    {
        $movies = Serie::select('series.id','series.name','series.poster_path',
        'series.vote_average','series.newEpisodes','series.subtitle')->where('created_at', '>', Carbon::now()->subMonth(3))
        ->where('active', '=', 1)
        ->orderByDesc('created_at')
        ->limit(10)->get();


    return response()->json(['recents' => $movies->makeHidden(['casterslist','casters','seasons','overview','backdrop_path','preview_path','videos'
    ,'substitles','vote_count','popularity','runtime','release_date','imdb_external_id','hd','pinned','preview'])], 200);

    }


     public function relateds(Serie $serie)
    {
        $genre = $serie->genres[0]->genre_id;

        $order = 'desc';
      
        $animes = Serie::join('serie_genres', 'serie_genres.serie_id', '=', 'series.id')
       ->where('genre_id', $genre)
        ->where('serie_id', '!=', $serie->id)->select('series.id','series.name','series.poster_path',
        'series.vote_average','series.newEpisodes','series.subtitle')
        ->limit(6)->get()->makeHidden(['casterslist','casters','seasons','overview','backdrop_path','preview_path','videos'
        ,'substitles','vote_count','popularity','runtime','release_date','imdb_external_id','hd','pinned','preview'
    ,'networkslist','genres','networks']);
       
       return response()->json(['relateds' => $animes], 200);
    }





    public function showEpisodeFromNotifcation($id)
    {



        $order = 'desc';
        $series = Serie::where('active', '=', 1)->join('seasons', 'seasons.serie_id', '=', 'series.id')
        ->join('episodes', 'episodes.season_id', '=', 'seasons.id')
        ->join('serie_videos', 'serie_videos.episode_id', '=', 'episodes.id')
        ->orderBy('serie_videos.updated_at', $order)->select('serie_videos.episode_id','series.id','series.tmdb_id as serieTmdb'
        ,'series.name as serieName','episodes.still_path','episodes.season_id','seasons.season_number','seasons.name as seasons_name','seasons.name','episodes.name as episode_name','serie_videos.link','serie_videos.server','serie_videos.lang'
        ,'serie_videos.embed','serie_videos.youtubelink','serie_videos.hls','seasons.name as seasons_name','seasons.season_number','episodes.vote_average'
        ,'series.premuim','episodes.episode_number','series.poster_path','episodes.hasrecap','episodes.skiprecap_start_in'
        ,'serie_videos.supported_hosts','serie_videos.header','serie_videos.useragent','series.imdb_external_id' ,
        'serie_videos.drmuuid','serie_videos.drmlicenceuri','serie_videos.drm'
        )->addSelect(DB::raw("'serie' as type"))->where('episodes.id', '=', $id)
        ->limit(1)->get()->makeHidden(['seasons','episodes','casterslist']);

        $newEpisodes = [];
        foreach ($series as $item) {
            array_push($newEpisodes, $item);
        }


        return response()->json(['latest_episodes' => $newEpisodes], 200);

    }

    public function newEpisodes()
    {
        $order = 'desc';
        $series = Serie::where('active', '=', 1)->join('seasons', 'seasons.serie_id', '=', 'series.id')
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
        foreach ($series->makeHidden(['seasons','casterslist','casters'])->toArray() as $item) {
            array_push($newEpisodes, $item);
        }

        return response()->json(['latest_episodes' => $newEpisodes], 200);

    }

    

    public function seriesEpisodesAll()
    {
        $order = 'desc';
        $series = Serie::where('active', '=', 1)->join('seasons', 'seasons.serie_id', '=', 'series.id')
        ->join('episodes', 'episodes.season_id', '=', 'seasons.id')
        ->join('serie_videos', 'serie_videos.episode_id', '=', 'episodes.id')
        ->orderBy('serie_videos.updated_at', $order)->orderBy('serie_videos.episode_id', $order)->select('serie_videos.episode_id','series.id'
        ,'series.name','episodes.still_path','episodes.season_id','episodes.name as episode_name','serie_videos.link','serie_videos.server','serie_videos.lang'
        ,'serie_videos.embed','serie_videos.youtubelink','serie_videos.hls','seasons.name as seasons_name','seasons.season_number','episodes.vote_average'
        ,'series.premuim','series.tmdb_id','episodes.episode_number',
        'series.poster_path','episodes.hasrecap','episodes.skiprecap_start_in',
        'serie_videos.supported_hosts','series.imdb_external_id','serie_videos.header','serie_videos.useragent'
        ,'serie_videos.drmuuid','serie_videos.drmlicenceuri','serie_videos.drm'
        )->addSelect(DB::raw("'serie' as type"))->groupBy('episode_id')->paginate(12);


        $series->setCollection($series->getCollection()->makeHidden(['seasons','episodes']));

        return $series;

        
        return response()->json($series, 200);

    }


}
