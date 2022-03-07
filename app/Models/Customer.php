<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;
    protected $table = "customers";
    protected $properties = ['name', 'fiscalId', 'customerType', 'address', 'city', 'postalCode', 'state', 'country', 'phone', 'email', 'birthDate'];    
    public function getProperties(){
        return $this->properties;
    }    
}