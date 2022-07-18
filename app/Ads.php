<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Ads extends Model
{


    protected $fillable = ['title', 'link', 'clickThroughUrl'];


}
