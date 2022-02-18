<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Store extends Model 
{
    use SoftDeletes;

    protected $table = "stores";
    protected $properties = ['name', 'address', 'city', 'postal_code', 'state', 'country', 'phone', 'email'];
    
    public function getProperties(){
        return $this->properties;
    }
}