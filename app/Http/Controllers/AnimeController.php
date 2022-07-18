<?php

namespace App\Http\Controllers;

use App\Embed;
use App\AnimeEpisode;
use App\AnimeSubstitle;
use App\AnimeCast;
use App\Genre;
use App\Cast;
use App\Network;
use App\Http\Requests\AnimeStoreRequest;
use App\Http\Requests\AnimeUpdateRequest;
use App\Http\Requests\StoreImageRequest;
use App\Jobs\SendNotification;
use App\AnimeSeason;
use App\Anime;
use App\AnimeGenre;
use App\AnimeVideo;
use App\AnimeDownload;
use App\AnimeNetwork;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnimeController extends Controller
{


    public function __construct()
    {
        $this->middleware('doNotCacheResponse', ['only' => ['moviecomment','addcomment']]);
    }



// returns all animes except children animes, for api.
public function index()
{
    $anime = Anime::whereDoesntHave('genres', function ($genre) {
        $genre->where('genre_id', '=', 10762);
    })->orderByDesc('id')->paginate(12);

    return response()->json($anime, 200);

}



public function moviecomment($movie)
{

    $movie = Anime::where('id', '=', $movie)->first();

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

    $movie = Anime::where('id', '=', $request->movie_id)->first();

    $comment = $movie->commentAsUser($user, $request->comments_message);

    return response()->json($comment, 200);

}

public function addtofav($id,Request $request)
{
    
    $serie = Anime::where('id', '=', $id)->first()->addFavorite($request->user()->id);

    return response()->json("Success", 200);

}



public function removefromfav($id,Request $request)
{

    $movie = Anime::where('id', '=', $id)->first()->removeFavorite($request->user()->id);

    return response()->json("Added", 200);

}


public function isMovieFavorite($id,Request $request)
{

    $movie = Anime::where('id', '=', $id)->first();

    if($movie->isFavorited($request->user()->id)) {

        $data = ['status' => 200, 'status' => 1,];

    }else {
        
        $data = ['status' => 400, 'status' => 0,];
    }

    return response()->json($data, 200);
}




// returns all animes for admin panel
public function data()
{
    return response()->json(Anime::with('seasons.episodes.videos','genres','casters','networks')->orderByDesc('created_at')
    ->paginate(6), 200);

}

// returns a specific anime
public function show($anime)
{

    $serie = Anime::query()
    ->with(['casters.cast' => function ($query) {
        $query->select('id', 'name','original_name','profile_path');
    }])->where('id', '=', $anime)->first()->makeHidden(['casters','networks']);

    $serie->increment('views',1);
    
    return response()->json($serie, 200);

}


// create a new anime in the database
public function store(AnimeStoreRequest $request)
{
    $anime = new Anime();
    $anime->fill($request->anime);
    $anime->save();


    $this->onSaveAnimeGenre($request,$anime);
    $this->onSaveAnimeSeasons($request,$anime);
    $this->onSaveAnimeCasters($request,$anime);
    $this->onSaveAnimeNetworks($request,$anime);



    if ($request->notification) {
        $this->dispatch(new SendNotification($anime));
    }

    $data = [
        'status' => 200,
        'message' => 'successfully created',
        'body' => $anime->load('seasons.episodes.videos')
    ];

    return response()->json($data, $data['status']);
}




public function onSaveAnimeNetworks($request,$anime) {

    if ($request->anime['networks']) {
        foreach ($request->anime['networks'] as $network) {
            $find = Network::find($network['id']);
            if ($find == null) {
                $find = new Network();
                $find->fill($network);
                $find->save();
            }
            $serieNetwork = new AnimeNetwork();
            $serieNetwork->network_id = $network['id'];
            $serieNetwork->anime_id = $anime->id;
            $serieNetwork->save();
        }
    }

}

public function onSaveAnimeCasters($request,$anime) {

    if ($request->anime['casterslist']) {
        foreach ($request->anime['casterslist'] as $cast) {
            $find = Cast::find($cast['id']);
            if ($find == null) {
                $find = new Cast();
                $find->fill($cast);
                $find->save();
            }
            $movieGenre = new AnimeCast();
            $movieGenre->cast_id = $cast['id'];
            $movieGenre->anime_id = $anime->id;
            $movieGenre->save();
        }
    }

}


public function onSaveAnimeGenre($request,$anime) {

    if ($request->anime['genres']) {
        foreach ($request->anime['genres'] as $genre) {
            $find = Genre::find($genre['id']);
            if ($find == null) {
                $find = new Genre();
                $find->fill($genre);
                $find->save();
            }
            $animeGenre = new AnimeGenre();
            $animeGenre->genre_id = $genre['id'];
            $animeGenre->anime_id = $anime->id;
            $animeGenre->save();
        }
    }

}


public function onSaveAnimeSeasons($request , $anime){

    if ($request->anime['seasons']) {
        foreach ($request->anime['seasons'] as $reqSeason) {
            $season = new AnimeSeason();
            $season->fill($reqSeason);
            $season->anime_id = $anime->id;
            $season->save();
           
            $this->onSaveEpisodes($request,$reqSeason,$season);

            
        }
    }

}


public function onSaveEpisodes($request, $reqSeason,$season) {

    if ($reqSeason['episodes']) {
        foreach ($reqSeason['episodes'] as $reqEpisode) {
            $episode = new AnimeEpisode();
            $episode->fill($reqEpisode);
            $episode->anime_season_id = $season->id;
            $episode->save();


            if (isset($reqEpisode['videos'])) {
                foreach ($reqEpisode['videos'] as $reqVideo) {
                    $video = AnimeVideo::find($reqVideo['id'] ?? 0) ?? new AnimeVideo();
                    $video->fill($reqVideo);
                    $video->anime_episode_id = $episode->id;
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
            $video = AnimeDownload::find($reqVideo['id'] ?? 0) ?? new AnimeDownload();
            $video->fill($reqVideo);
            $video->anime_episode_id = $episode->id;
            $video->save();
        }
    }

}


public function onSaveEpisodeSubstitle($request,$reqEpisode,$episode) {


    if (isset($reqEpisode['substitles'])) {
               foreach ($reqEpisode['substitles'] as $reqVideo) {
                   $video = new AnimeSubstitle();
                   $video->fill($reqVideo);
                   $video->anime_episode_id = $episode->id;
                   $video->save();
               }
           }
}



// update a anime in the database
public function update(AnimeUpdateRequest $request, Anime $anime)
{

    $anime->fill($request->anime);
    $anime->save();

    $this->onUpdateAnimeGenre($request,$anime);
    $this->onUpdateAnimeSeasons($request,$anime);
    $this->onUpdateAnimeCasts($request,$anime);
    $this->onUpdateAnimeNetwork($request,$anime);

    $data = [
        'status' => 200,
        'message' => 'successfully updated',
        'body' => "Success"
    ];

    return response()->json($data, $data['status']);
}





public function onUpdateAnimeNetwork ($request,$anime) {

    if ($request->anime['networks']) {
        foreach ($request->anime['networks'] as $netwok) {
            if (!isset($netwok['network_id'])) {
                $find = Network::find($netwok['id']) ?? new Network();
                $find->fill($netwok);
                $find->save();
                $serieNetwork = AnimeNetwork::where('anime_id', $anime->id)->where('network_id', $netwok['id'])->get();
                if (count($serieNetwork) < 1) {
                    $serieNetwork = new AnimeNetwork();
                    $serieNetwork->network_id = $netwok['id'];
                    $serieNetwork->anime_id = $anime->id;
                    $serieNetwork->save();
                }
            }
        }
    }

}


public function onUpdateAnimeCasts ($request,$anime) {


    if ($request->anime['casterslist']) {
        foreach ($request->anime['casterslist'] as $genre) {
            
                $find = Cast::find($genre['id'] ?? 0) ?? new Cast();
                $find->fill($genre);
                $find->save();
                $movieGenre = AnimeCast::where('anime_id', $anime->id)
                    ->where('cast_id', $genre['id'])->get();

                if (count($movieGenre) < 1) {
                    $movieGenre = new AnimeCast();
                    $movieGenre->cast_id = $genre['id'];
                    $movieGenre->anime_id = $anime->id;
                    $movieGenre->save();
            
                }
            
        }
    }

}


public function onUpdateAnimeGenre ($request,$anime) {

    if ($request->anime['genres']) {
        foreach ($request->anime['genres'] as $genre) {
            if (!isset($genre['genre_id'])) {
                $find = Genre::find($genre['id']) ?? new Genre();
                $find->fill($genre);
                $find->save();
                $animeGenre = AnimeGenre::where('anime_id', $anime->id)->where('genre_id', $genre['id'])->get();
                if (count($animeGenre) < 1) {
                    $animeGenre = new AnimeGenre();
                    $animeGenre->genre_id = $genre['id'];
                    $animeGenre->anime_id = $anime->id;
                    $animeGenre->save();
                }
            }
        }
    }

}


public function onUpdateAnimeSeasons($request,$anime){


    if ($request->anime['seasons']) {
        foreach ($request->anime['seasons'] as $reqSeason) {
            $season = AnimeSeason::find($reqSeason['id'] ?? 0) ?? new AnimeSeason();
            $season->fill($reqSeason);
            $season->anime_id = $anime->id;
            $season->save();


            $this->onUpdateAnimeEpisodes($request,$reqSeason,$season);
            
        }
    }
}




public function onUpdateAnimeEpisodes ($request,$reqSeason,$season) {

    if ($reqSeason['episodes']) {
                foreach ($reqSeason['episodes'] as $reqEpisode) {
                    $episode = AnimeEpisode::find($reqEpisode['id'] ?? 0) ?? new AnimeEpisode();
                    $episode->fill($reqEpisode);
                    $episode->anime_season_id = $season->id;
                    $episode->save();
                    if (isset($reqEpisode['videos'])) {
                        foreach ($reqEpisode['videos'] as $reqVideo) {
                            $video = AnimeVideo::find($reqVideo['id'] ?? 0) ?? new AnimeVideo();
                            $video->fill($reqVideo);
                            $video->anime_episode_id = $episode->id;
                            $video->save();
                        }
                    }

                    $this->onUpdateAnimeSubstitle($request,$reqEpisode,$episode);
                    $this->onUpdateAnimeDownload($request,$reqEpisode,$episode);
                }
            }

}


public function onUpdateAnimeDownload ($request,$reqEpisode,$episode) {

    if (isset($reqEpisode['downloads'])) {
        foreach ($reqEpisode['downloads'] as $reqVideo) {

            $substitle = AnimeDownload::find($reqVideo['id'] ?? 0) ?? new AnimeDownload();
            $substitle->fill($reqVideo);
            $substitle->anime_episode_id = $episode->id;
            $substitle->save();
        }
    
}

}

public function onUpdateAnimeSubstitle ($request,$reqEpisode,$episode) {

    if (isset($reqEpisode['substitles'])) {
        foreach ($reqEpisode['substitles'] as $reqVideo) {

            $substitle = AnimeSubstitle::find($reqVideo['id'] ?? 0) ?? new AnimeSubstitle();
            $substitle->fill($reqVideo);
            $substitle->anime_episode_id = $episode->id;
            $substitle->save();
        }
    
}

}

// delete a anime from the database

    public function destroy(Anime $anime)
    {
        if ($anime != null) {
            $anime->delete();

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


// remove a genre from a animes from the database
public function destroyGenre($genre)
{
    if ($genre != null) {

        AnimeGenre::find($genre)->delete();

        $data = ['status' => 200, 'message' => 'successfully deleted',];
    } else {
        $data = ['status' => 400, 'message' => 'could not be deleted',];
    }

    return response()->json($data, 200);
}

// save a new image in the animes folder of the storage
public function storeImg(StoreImageRequest $request)
{

    if ($request->hasFile('image')) {
        $filename = Storage::disk('animes')->put('', $request->image);
        $data = [
            'status' => 200,
            'image_path' => $request->root() . '/api/animes/image/' . $filename,
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

// return an image from the animes folder of the storage
public function getImg($filename)
{

    $image = Storage::disk('animes')->get($filename);

    $mime = Storage::disk('animes')->mimeType($filename);

    return (new Response($image, 200))
        ->header('Content-Type', $mime);
}


// returns a specific anime
public function showbyimdb($anime)
{

    $anime_by_imdbid = Anime::where('tmdb_id', '=', $anime)->first();


    return response()->json($anime_by_imdbid, 200);
}


// returns the last 10 animes added in the month
public function recents()
{



    $movies = Anime::select('animes.id','animes.name','animes.poster_path','animes.vote_average',
    'animes.is_anime','animes.vote_average','animes.newEpisodes','animes.subtitle')
    ->where('created_at', '>', Carbon::now()->subMonth(3))
        ->where('active', '=', 1)
        ->orderByDesc('created_at')
        ->limit(10)->get();



    return response()->json(['anime' => $movies->makeHidden(['casterslist','casters','seasons','overview','backdrop_path','preview_path','videos'
    ,'substitles','vote_count','popularity','runtime','release_date','imdb_external_id','hd','pinned','preview'])], 200);

}




// remove Network from  a movie
public function destroyNetworks($id)
{

    if ($id != null) {

        AnimeNetwork::find($id)->delete();
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

          $movie = AnimeCast::where('cast_id', '=', $id)->first();
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

public function relateds(Anime $anime)
{
    $genre = $anime->genres[0]->genre_id;
    
    $animes = Anime::whereHas('genres', function ($query) use ($genre) {
        $query->where('genre_id', '=', $genre)->where('anime_id', '!=', $anime->id);
    })->select('animes.name','animes.id','animes.poster_path','animes.vote_average','animes.subtitle','animes.is_anime')->where('active', '=', 1)
    ->limit(5)->get();

return response()
->json(['relateds' => 
$animes->makeHidden(['seasons','videos','casterslist','casters','genres','networkslist','downloads','networks','substitles'])
->toArray()], 200);
}


public function newEpisodes()
{
    $order = 'desc';
   
    $animes = Anime::where('active', '=', 1)->join('anime_seasons', 'anime_seasons.anime_id', '=', 'animes.id')
    ->join('anime_episodes', 'anime_episodes.anime_season_id', '=', 'anime_seasons.id')
    ->join('anime_videos', 'anime_videos.anime_episode_id', '=', 'anime_episodes.id')
    ->orderBy('anime_videos.updated_at', $order)->orderBy('anime_videos.anime_episode_id', $order)->select('anime_videos.anime_episode_id','animes.id'
    ,'animes.name','anime_episodes.still_path','anime_episodes.anime_season_id','anime_episodes.name as episode_name','anime_videos.link','anime_videos.server','anime_videos.lang'
    ,'anime_videos.embed','anime_videos.youtubelink','anime_videos.hls','anime_seasons.name as seasons_name','anime_seasons.season_number','anime_episodes.vote_average'
    ,'animes.premuim','animes.tmdb_id','anime_episodes.episode_number','animes.poster_path',
    'anime_episodes.hasrecap',
    'anime_episodes.skiprecap_start_in','anime_videos.supported_hosts'
    ,'anime_videos.drmuuid','anime_videos.drmlicenceuri','anime_videos.drm'
    )->addSelect(DB::raw("'anime' as type"))->limit(10)->get()->unique('anime_episode_id')->makeHidden(['seasons','episodes','casterslist','']);


    $newEpisodes = [];
    foreach ($animes as $item) {
        array_push($newEpisodes, $item);
    }

    return response()->json(['latest_episodes' => $newEpisodes], 200);

}



public function animesEpisodesAll()
{
    $order = 'desc';
   
    $animes = Anime::where('active', '=', 1)->join('anime_seasons', 'anime_seasons.anime_id', '=', 'animes.id')
    ->join('anime_episodes', 'anime_episodes.anime_season_id', '=', 'anime_seasons.id')
    ->join('anime_videos', 'anime_videos.anime_episode_id', '=', 'anime_episodes.id')
    ->orderBy('anime_videos.updated_at', $order)->orderBy('anime_videos.anime_episode_id', $order)->select('anime_videos.anime_episode_id','animes.id','animes.tmdb_id as serieTmdb'
    ,'animes.name','anime_episodes.still_path','anime_episodes.anime_season_id','anime_episodes.name as episode_name','anime_videos.link','anime_videos.server','anime_videos.lang'
    ,'anime_videos.embed','anime_videos.youtubelink','anime_videos.hls','anime_seasons.name as seasons_name','anime_seasons.season_number','anime_episodes.vote_average'
    ,'animes.premuim','animes.tmdb_id','anime_episodes.episode_number','animes.poster_path',
    'anime_episodes.hasrecap','anime_episodes.skiprecap_start_in','anime_videos.supported_hosts','animes.is_anime'
    ,'anime_videos.drmuuid','anime_videos.drmlicenceuri','anime_videos.drm'
    )->addSelect(DB::raw("'anime' as type"))->groupBy('anime_episode_id')->paginate(12);



    $animes->setCollection($animes->getCollection()->makeHidden(['seasons','episodes','casterslist']));
    return $animes;


    return response()->json(['latest_episodes' => $animes], 200);

}



public function showEpisodeFromNotifcation($id)
{

    $order = 'desc';
    $animes = Anime::where('active', '=', 1)->join('anime_seasons', 'anime_seasons.anime_id', '=', 'animes.id')
    ->join('anime_episodes', 'anime_episodes.anime_season_id', '=', 'anime_seasons.id')
    ->join('anime_videos', 'anime_videos.anime_episode_id', '=', 'anime_episodes.id')
    ->orderBy('anime_videos.updated_at', $order)->orderBy('anime_videos.anime_episode_id', $order)->select('anime_videos.anime_episode_id','animes.id'
    ,'animes.name','anime_episodes.still_path','anime_episodes.anime_season_id','anime_episodes.name as episode_name','anime_videos.link','anime_videos.server','anime_videos.lang'
    ,'anime_videos.embed','anime_videos.hls','anime_seasons.name as seasons_name','anime_seasons.season_number','anime_episodes.vote_average'
    ,'animes.premuim','animes.tmdb_id','anime_episodes.episode_number','animes.poster_path',
    'anime_episodes.hasrecap','anime_episodes.skiprecap_start_in',
    'anime_videos.supported_hosts','animes.imdb_external_id','anime_videos.header'
    ,'anime_videos.useragent','animes.imdb_external_id','anime_videos.drmuuid','anime_videos.drmlicenceuri','anime_videos.drm'
    )->addSelect(DB::raw("'anime' as type"))->where('anime_episodes.id', '=', $id)->limit(1)
    ->get()->makeHidden(['seasons','episodes','casterslist']);


    return response()->json(['latest_episodes' => $animes], 200);

}


}
