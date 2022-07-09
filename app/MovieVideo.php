<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MovieVideo extends Model
{
    protected $fillable = ['server','header','useragent','linktype','link','video_name', 'lang', 'embed','youtubelink','supported_hosts'
    ,'hls', 'status','drmuuid','drmlicenceuri','drm'];


    protected $casts = [
        'embed' => 'int',
        'youtubelink' => 'int',
        'supported_hosts' => 'int',
        'alldebrid_supported_hosts' => 'int',
        'hls' => 'int',
        'downloadonly' => 'int'

    ];


    public function movie()
    {
        return $this->belongsTo('App\Movie');
    }

}
