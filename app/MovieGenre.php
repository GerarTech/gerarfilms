<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MovieGenre extends Model
{


   protected $appends = ['name'];

   protected $hidden = [
    'genre'];

    public function genre()
    {
        return $this->belongsTo('App\Genre', 'genre_id');
    }

    public function movie()
    {
        return $this->belongsTo('App\Movie', 'movie_id');
    }


    public function getNameAttribute()
    {


        return $this->genre->name;
 
    }

}
