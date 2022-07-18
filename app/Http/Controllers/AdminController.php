<?php

namespace App\Http\Controllers;

use App\Episode;
use App\Livetv;
use App\Movie;
use App\Serie;
use App\Anime;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;


class AdminController extends Controller
{

    const FEATURED = "featured";
    const CREATED_AT = "created_at";
    const VIEWS = "views";


    public function __construct()
    {
        $this->middleware('auth');
    }

    // navigation routes for the admin panel


    

    public function home()
    {
        return view('admin.home');
    }

    public function index()
    {
        return view('admin.index');
    }



    public function comments()
    {
        return view('admin.comments');
    }

    public function users()
    {
        return view('admin.users');
    }

    public function movies()
    {
        return view('admin.movies');
    }

    public function series()
    {
        return view('admin.series');
    }


    public function animes()
    {
        return view('admin.animes');
    }


    public function streaming()
    {
        return view('admin.streaming');
    }

    public function servers()
    {
        return view('admin.servers');
    }



    public function headers()
    {
        return view('admin.headers');
    }

    public function genres()
    {
        return view('admin.genres');
    }


    public function networks()
    {
        return view('admin.networks');
    }

    public function casters()
    {
        return view('admin.casters');
    }



    public function notifications()
    {
        return view('admin.notifications');
    }

    public function settings()
    {
        return view('admin.settings');
    }

    public function account()
    {
        return view('admin.account');
    }


    public function reports()
    {
        return view('admin.reports');
    }



    public function suggestions()
    {
        return view('admin.suggestions');
    }


    public function ads()
    {
        return view('admin.ads');
    }


    public function upcomings()
    {
        return view('admin.upcomings');
    }

    public function plans()
    {
        return view('admin.plans');
    }



    public function categories()
    {
        return view('admin.categories');
    }

    public function previews()
    {
        return view('admin.previews');
    }


    public function featured()

    {


        return view('admin.featured');

    }



    
    public function topcontentmovies()

    {

        $movies = Movie::select('movies.id','movies.title','movies.poster_path','movies.views')->where('active', '=', 1)
        ->orderByDesc('views')->limit(5)->get();
      
        return response()
            ->json($movies->makeHidden(['videos','casterslist','casters','genres','genreslist','substitles'])
            ->toArray(), 200);

    }




        
    public function topcontentseries()

    {

        $movies = Serie::select('series.id','series.name','series.poster_path','series.views')->where('active', '=', 1)
        ->orderByDesc('views')->limit(5)->get();
      
        return response()
            ->json($movies->makeHidden(['seasons','casterslist','casters','genres','genreslist','substitles'])->toArray(), 200);

    }


    public function topcontentanimes()

    {

        $movies = Anime::select('animes.id','animes.name','animes.poster_path','animes.views')->where('active', '=', 1)
        ->orderByDesc('views')->limit(5)->get();
      
        return response()
            ->json($movies->makeHidden(['seasons','casterslist','casters','genres','genreslist','substitles'])->toArray(), 200);

    }



    // most viewed metrics

    public function topMovies()
    {
        $movies = Movie::orderBy(self::VIEWS, 'desc')->limit(5)->get();

        return response()->json($movies, 200);
    }

    public function topSeries()
    {
        $series = anime::all()->makeHidden(['seasons', 'genres'])->sortByDesc(self::VIEWS);

        if ($series->count() > 10) {
            $series = $series->take(10);
        }

        return response()->json($animes, 200);
    }

    public function topEpisodes()
    {
        $episodes = Episode::orderBy(self::VIEWS, 'desc')->limit(10)->get();

        return response()->json($episodes, 200);
    }

    public function topLivetv()
    {
        $livetv = Livetv::orderBy(self::VIEWS, 'desc')->limit(10)->get();

        return response()->json($livetv, 200);
    }

    public function topUsers()
    {
        $users = User::orderBy('id', 'desc')->limit(10)->get();

        return response()->json($users, 200);
    }



    public function moviesCountViews()
    {
        return response()->json([
            'status' => 'success',
            'data' => [

                'count' => [
                    'movies' => DB::table('movies')->sum('views'),
                    'series' => DB::table('series')->sum('views'),
                    'animes' => DB::table('animes')->sum('views'),
                    'tvs' => DB::table('livetvs')->sum('views'),

                ],

            ]
        ]);
    }


    public function moviesInactiveCount()
    {
        return response()->json([
            'status' => 'success',
            'data' => [

                'count' => [
                    'movies' => DB::table('movies')->where('active', '=', 1)->count(),
                    'series' => DB::table('series')->where('active', '=', 0)->count(),
                    'animes' => DB::table('animes')->where('active', '=', 0)->where('active', '=', 0)->count(),
                    'tvs' => DB::table('livetvs')->where('active', '=', 0)->count()
                ],

            ]
        ]);
    }

    public function moviesCount()
    {
        return response()->json([
            'status' => 'success',
            'data' => [

                'count' => [
                    'reports' => DB::table('reports')->count(),
                    'movies' => DB::table('movies')->count(),
                    'series' => DB::table('series')->count(),
                    'animes' => DB::table('animes')->count(),
                    'tvs' => DB::table('livetvs')->count(),
                    'users' => DB::table('users')->count()
                ],

            ]
        ]);
    }

    public function updateMailSettings(Request $request){
        // some code
        $env_update = $this->changeEnv([
            'MAIL_DRIVER'   => 'sendmail',
            'MAIL_HOST'   => $request->get('email_smtp_adress'),
            'MAIL_PORT'       => $request->get('email_smtp_port')
        ]);
        if($env_update){
            // Do something
        } else {
            // Do something else
        }
        // more code
    }
    
    protected function changeEnv($data = array()){
        if(count($data) > 0){

            // Read .env-file
            $env = file_get_contents(base_path() . '/.env');

            // Split string on every " " and write into array
            $env = preg_split('/\s+/', $env);;

            // Loop through given data
            foreach((array)$data as $key => $value){

                // Loop through .env-data
                foreach($env as $env_key => $env_value){

                    // Turn the value into an array and stop after the first split
                    // So it's not possible to split e.g. the App-Key by accident
                    $entry = explode("=", $env_value, 2);

                    // Check, if new key fits the actual .env-key
                    if($entry[0] == $key){
                        // If yes, overwrite it with the new one
                        $env[$env_key] = $key . "=" . $value;
                    } else {
                        // If not, keep the old one
                        $env[$env_key] = $env_value;
                    }
                }
            }

            // Turn the array back to an String
            $env = implode("\n", $env);

            // And overwrite the .env with the new data
            file_put_contents(base_path() . '/.env', $env);
            
            return true;
        } else {
            return false;
        }
    }


}
