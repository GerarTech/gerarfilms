<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LivetvGenre extends Model

{

    protected $appends = ['name'];

    protected $hidden = [
     'genre'];
    
    public function genre()
    {
        return $this->belongsTo('App\Category', 'category_id');
    }

    public function livetv()
    {
        return $this->belongsTo('App\Livetv', 'livetv_id');
    }



    public function getNameAttribute()
    {
        return $this->genre->name;
    }



}
