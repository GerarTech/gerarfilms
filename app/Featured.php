<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Http\ClearsResponseCache;

class Featured extends Model
{


        use ClearsResponseCache;
     
        protected $fillable = ['featured_id','title', 'type', 'poster_path','miniposter', 'genre','premuim'
        ,'enable_miniposter','position','custom','release_date','vote_average','custom_link'];


        protected $casts = [

                'premuim' => 'int',
                'enable_miniposter' => 'int'
            ];


}
