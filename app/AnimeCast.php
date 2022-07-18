<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AnimeCast extends Model
{
    protected $appends = ['name'];


    protected $hidden = [
        'cast'
    ];


    public function cast()
    {
        return $this->belongsTo('App\Cast', 'cast_id');
    }

    public function anime()
    {
        return $this->belongsTo('App\Serie', 'anime_id');
    }

    public function getNameAttribute()
    {
        return $this->cast->name;
    }

}
