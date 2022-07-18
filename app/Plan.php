<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Plan extends Model
{


    protected $fillable = ['name','description', 'price','stripe_plan_id','stripe_price_id','pack_duration','currency_plan_app'];



}
