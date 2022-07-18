<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SerieNetwork extends Model
{
    protected $appends = ['name'];


    protected $hidden = [
        'network'];


    public function network()
    {

    return $this->belongsTo('App\Network', 'network_id');

    }

    public function serie()
    {
        return $this->belongsTo('App\Serie', 'serie_id');
    }

    public function getNameAttribute()
    {
        return $this->network->name;
    }
}
