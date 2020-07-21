<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BuyDelivery extends Model 
{
    use SoftDeletes;

    protected $table = "buy_delivery";
}