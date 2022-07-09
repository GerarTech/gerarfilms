<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Statistic extends Model
{


    protected $fillable = ['installs'];


    protected $guarded = [];

    protected $casts = [
        'installs' => 'int'
    ];
}
