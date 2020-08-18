<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Components extends Model
{
    use SoftDeletes;

    protected $table = "components";

}