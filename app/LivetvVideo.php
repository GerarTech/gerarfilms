<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LivetvVideo extends Model
{
    protected $fillable = ['server','header','useragent','linktype','link','livetv_name', 'lang', 
    'embed','youtubelink','supported_hosts','hls', 'status','drmuuid','drmlicenceuri','drm'];


    

    protected $casts = [
        'embed' => 'int',
        'youtubelink' => 'int',
        'supported_hosts' => 'int',
        'hls' => 'int',
        'drm' => 'int'

    ];


    public function livetv()
    {
        return $this->belongsTo('App\Livetv');
    }

}
