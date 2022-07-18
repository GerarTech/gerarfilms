<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SerieCast extends Model
{
    protected $appends = ['name'];

    protected $hidden = [
        'cast'
    ];

    public function cast()
    {
        return $this->belongsTo('App\Cast', 'cast_id');
    }

    public function serie()
    {
        return $this->belongsTo('App\Serie', 'serie_id');
    }

    public function getNameAttribute()
    {
        return $this->cast->name;
    }

}
