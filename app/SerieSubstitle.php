<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SerieSubstitle extends Model
{
    protected $fillable = ['episode_id', 'server', 'link','type', 'lang','zip'];

    public function episode()
    {
        return $this->belongsTo('App\Episode');
    }
}
