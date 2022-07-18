<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SerieDownload extends Model
{
      protected $fillable = ['episode_id', 'server','header','useragent','link','lang','youtubelink','external','supported_hosts','video_name'];



      protected $casts = [
        'youtubelink' => 'int',
        'supported_hosts' => 'int',
        'external' => 'int'

    ];


    public function episode()
    {
        return $this->belongsTo('App\Episode');
    }

}
