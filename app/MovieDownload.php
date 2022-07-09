<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MovieDownload extends Model
{
    protected $fillable = ['server','header','useragent','link', 'lang','youtubelink','supported_hosts','external', 'status'];


    protected $casts = [
        'youtubelink' => 'int',
        'supported_hosts' => 'int',
        'alldebrid_supported_hosts' => 'int',
        'external' => 'int'

    ];


    public function movie()
    {
        return $this->belongsTo('App\Movie');
    }

}
