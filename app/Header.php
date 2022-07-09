<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Header extends Model
{
    protected $fillable = ['name'];

    public function movie()
    {
        return $this->belongsTo('App\Movie');
    }
}
