<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Server extends Model
{
    protected $fillable = ['name'];

    public function movie()
    {
        return $this->belongsTo('App\Movie');
    }
}
