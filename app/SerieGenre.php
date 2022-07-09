<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SerieGenre extends Model
{
    protected $appends = ['name'];


    protected $hidden = [
        'genre'];

    public function genre()
    {
        return $this->belongsTo('App\Genre', 'genre_id');
    }

    public function serie()
    {
        return $this->belongsTo('App\Serie', 'serie_id');
    }

    public function getNameAttribute()
    {
        return $this->genre->name;
    }
}
