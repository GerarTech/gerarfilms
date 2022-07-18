<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use BeyondCode\Comments\Traits\HasComments;

class Episode extends Model
{
    protected $fillable = ['tmdb_id', 'episode_number', 'name', 'overview', 'still_path', 'vote_average', 'vote_count',
     'air_date','hasrecap','skiprecap_start_in','free'];


    protected $with = ['videos', 'substitles','downloads'];

    use HasComments;

    protected $casts = [
        'hasrecap' => 'int',
        'skiprecap_start_in' => 'int'

    ];

    public function season(){
        return $this->belongsTo(Season::class, 'season_id');
     }
 
     public function videos() {
         return $this->hasMany('App\SerieVideo');
     }

     
    public function downloads()
    {
        return $this->hasMany('App\SerieDownload');
    }

    public function substitles()
    {
        return $this->hasMany('App\SerieSubstitle');
    }


}
