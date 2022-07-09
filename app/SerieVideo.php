<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SerieVideo extends Model
{
    protected $fillable = ['episode_id','server','header','useragent','link','embed','youtubelink', 'lang','hls','supported_hosts',
    'drmuuid','drmlicenceuri','drm'];



    protected $casts = [
        'embed' => 'int',
        'youtubelink' => 'int',
        'supported_hosts' => 'int',
        'hls' => 'int',
        'drm' => 'int'

    ];

    public function episode()
    {
        return $this->belongsTo('App\Episode');
    }
}
