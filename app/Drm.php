<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Drm extends Model
{
    protected $fillable = ['name'];

    public function movie()
    {
        return $this->belongsTo('App\Movie');
    }
}
