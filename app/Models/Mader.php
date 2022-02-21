<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mader extends Model 
{
    use SoftDeletes;

    protected $table = "maders";
    protected $properties = ['fiscalId', 'name', 'address', 'city', 'postalCode', 'state', 'country', 'phone', 'email', 'site'];
    
    public function getProperties(){
        return $this->properties;
    }
}