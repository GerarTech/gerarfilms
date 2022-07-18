<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Season extends Model
{
    protected $fillable = ['serie_id', 'tmdb_id', 'season_number', 'name', 'overview', 'poster_path', 'air_date'];

 
    protected $with = ['episodes'];

    public function episodes()
    {
        return $this->hasMany(Episode::class)->orderBy('episode_number');

    }


}
