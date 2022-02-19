<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Location extends Model 
{
    use SoftDeletes;

    protected $table = "locations";
    protected $properties = ['storeId', 'name'];
    public function getProperties(){
        return $this->properties;
    }
}