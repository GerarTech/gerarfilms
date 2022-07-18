<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
use App\Http\ClearsResponseCache;
use ChristianKuri\LaravelFavorite\Traits\Favoriteable;

class Livetv extends Model


{

    use Favoriteable,ClearsResponseCache;

    protected $fillable = ['name', 'overview', 'poster_path', 'backdrop_path', 'link','vip','active','featured','embed','hls'];

    protected $with = ['genres.genre','videos'];

    protected $casts = [
        'featured' => 'int',
        'embed' => 'int',
        'status' => 'int',
        'live' => 'int',
        'active' => 'int',
        'vip' => 'int',
        'hls' => 'int',
        'views' => 'int'
    ];

    public function genres()
    {
        return $this->hasMany('App\LivetvGenre');
    }


    public function videos()
    {
        return $this->hasMany('App\LivetvVideo');
    }
}
