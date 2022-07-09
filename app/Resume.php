<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Resume extends Model
{


    protected $fillable = ['user_resume_id','tmdb', 'resumeWindow','resumePosition','movieDuration','deviceId'];


}
