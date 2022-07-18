<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MovieNetwork extends Model
{
    protected $appends = ['name'];


    protected $hidden = [
        'network'];

    public function network()
    {

    return $this->belongsTo('App\Network', 'network_id');

    }

    public function movie()
    {
        return $this->belongsTo('App\Movie', 'movie_id');
    }

    public function getNameAttribute()
    {
        return $this->network->name;
    }
}
