<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ModelVh extends Model 
{
    use SoftDeletes;
    protected $table = "models";
     protected $properties = ['brandId', 'name'];
    public function getProperties(){
        return $this->properties;
    }
}