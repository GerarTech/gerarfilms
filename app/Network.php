<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Http\ClearsResponseCache;

class Network extends Model
{

    use ClearsResponseCache;

    protected $guarded = [];

    public function movies()
    {
        return $this->hasMany('App\MovieGenre');
    }


     public function upcomings()
    {
        return $this->hasMany('App\UpcomingGenre');
    }

    public function series()
    {
        return $this->hasMany('App\SerieNetwork');
    }

}
