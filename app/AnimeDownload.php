<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AnimeDownload extends Model
{
      protected $fillable = ['anime_episode_id', 'server','header','useragent', 'link','lang','youtubelink','external','supported_hosts','video_name'];



      protected $casts = [
        'youtubelink' => 'int',
        'supported_hosts' => 'int',
        'external' => 'int'

    ];


    public function episode()
    {
        return $this->belongsTo('App\AnimeEpisode');
    }

}
