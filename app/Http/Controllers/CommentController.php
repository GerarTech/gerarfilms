<?php

namespace App\Http\Controllers;

use App\Movie;
use App\Serie;
use App\Anime;
use App\Episode;
use App\AnimeEpisode;
use Illuminate\Http\Request;
use BeyondCode\Comments\Comment;
use Illuminate\Support\Facades\DB;

class CommentController extends Controller
{



    public function allcomments()
    {


        $order = 'desc';


        $movies = Movie::where('active', '=', 1)
        ->join('comments', 'comments.commentable_id', '=', 'movies.id')
        ->whereHas('comments', function ($query) use ($project) {
            $query->whereNotNull('comment');
           })
        ->select('movies.id','movies.title','comments.comment'
        ,'comments.user_name','comments.user_image')
        ->orderByDesc('comments.updated_at')
        ->addSelect(DB::raw("'movie' as type"));

      
        $series = Serie::where('active', '=', 1)
        ->join('comments', 'comments.commentable_id', '=', 'series.id')
        ->whereHas('comments', function ($query) use ($project) {
            $query->whereNotNull('comment');
           })
        ->select('series.id','series.name as title',
        'comments.comment','comments.user_name','comments.user_image')
        ->orderByDesc('comments.updated_at')
        ->addSelect(DB::raw("'serie' as type"));


        $animes = Anime::where('active', '=', 1)
        ->join('comments', 'comments.commentable_id', '=', 'animes.id')
        ->whereHas('comments', function ($query) use ($project) {
            $query->whereNotNull('comment');
           })
        ->select('animes.id','animes.name as title',
        'comments.comment','comments.user_name','comments.user_image')
        ->orderByDesc('comments.updated_at')
        ->addSelect(DB::raw("'anime' as type"));



         $episodes = Episode::join('comments', 'comments.commentable_id', '=',
        'episodes.id')->select('episodes.id','episodes.name as title'
        ,'comments.comment','comments.user_name','comments.user_image'
        )->whereHas('comments', function ($query) use ($project) {
            $query->whereNotNull('comment');
           })
           ->orderByDesc('comments.updated_at')
        ->addSelect(DB::raw("'episode' as type"));


        
        $animeEpisodes = AnimeEpisode::join('comments', 'comments.commentable_id', '=',
        'anime_episodes.id')->select('anime_episodes.id','anime_episodes.name as title'
        ,'comments.comment','comments.user_name','comments.user_image'
        )->whereHas('comments', function ($query) use ($project) {
            $query->whereNotNull('comment');
           })
        ->addSelect(DB::raw("'anime_episode' as type"));

        $query = $movies->union($series)->union($animes)
        ->union($episodes)->union($animeEpisodes);


        return response()->json($query->paginate(12), 200);
    }

    
}
