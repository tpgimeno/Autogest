<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SellOffer extends Model
{
    use SoftDeletes;
    protected $table = 'sellOffers';
}
        