<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Http\ClearsResponseCache;

class Cast extends Model
{


    use ClearsResponseCache;

    protected $guarded = [];


    public function movies()
    {
        return $this->hasMany('App\MovieCast');
    }
    

}
