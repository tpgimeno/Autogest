<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mader extends Model 
{
    use SoftDeletes;

    protected $table = "maders";
}