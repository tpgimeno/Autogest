<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sellers extends Model 
{
    use SoftDeletes;
    protected $table = "sellers";
    protected $properties = ['name', 'fiscalId', 'address', 'city', 'postalCode', 'state', 'country', 'phone', 'email', 'birthDate'];
    
    public function getProperties(){
        return $this->properties;
    }
}