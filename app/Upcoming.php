<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Upcoming extends Model
{
     
        protected $fillable = ['tmdb_id','title', 'overview', 'poster_path', 'backdrop_path','genre', 'trailer_id','release_date'];


}
