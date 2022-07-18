<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
use ChristianKuri\LaravelFavorite\Traits\Favoriteable;
use App\Http\ClearsResponseCache;
use BeyondCode\Comments\Traits\HasComments;


class Movie extends Model
{


    protected $with = ['casters.cast','genres.genre','videos','downloads','substitles','networks.network'];

    use Favoriteable,ClearsResponseCache,HasComments;

    protected $fillable = ['tmdb_id','imdb_external_id', 'title', 'overview', 'poster_path', 'backdrop_path', 'preview_path',
     'vote_average', 'vote_count', 'popularity', 'runtime', 'views','featured','pinned', 'premuim','hasrecap','skiprecap_start_in','active', 'release_date'
    ,'minicover','linkpreview','preview','original_name','trailer_url','subtitle'];

    protected $appends = ['casterslist','substype','networkslist'];

    protected $casts = [
        'status' => 'int',
        'premuim' => 'int',
        'skiprecap_start_in' => 'int',
        'hasrecap' => 'int',
        'featured' => 'int',
        'pinned' => 'int',
        'active' => 'int',
        'preview' => 'int'

    ];


    public function casters()
    {
        return $this->hasMany('App\MovieCast');
    }


    public function networks()
    {
        return $this->hasMany('App\MovieNetwork');
    }


    public function genres()
    {
        return $this->hasMany('App\MovieGenre');
    }

    public function videos()
    {
        return $this->hasMany('App\MovieVideo');
    }


    public function downloads()
    {
        return $this->hasMany('App\MovieDownload');
    }


    public function substitles()
    {
        return $this->hasMany('App\MovieSubstitle');
    }



    public function getSubsTypeAttribute()
    {
        $substype = 0;
        $substitles = $this->substitles;
        if ($substitles) {
            foreach ($substitles as $substitle) {
                if ($substitle->type) {
                    $substype = $substitle->type;
                }
            }
        }

        return $substype;
    }


    public function getCasterslistAttribute()
    {
        $casters = [];
        foreach ($this->casters as $caster) {
            array_push($casters, $caster->cast);
        }
        return $casters;
    }

    public function getNetworkslistAttribute()
    {
        $networks = [];
        foreach ($this->networks as $network) {
            array_push($networks, $network->network);
        }
        return $networks;
    }

}
