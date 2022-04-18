<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VehicleTypes extends Model 
{
    use SoftDeletes;
    protected $table = "vehicletypes";
    protected $properties = ['name'];
    public function getProperties(){
        return $this->properties;
    }
}