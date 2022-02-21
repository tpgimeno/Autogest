<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Garage extends Model 
{
    use SoftDeletes;

    protected $table = "garages";
    protected $properties = ['name', 
        'fiscalId', 
        'fiscalName', 
        'address',        
        'city',
        'postalCode',
        'state', 
        'country', 
        'phone',
        'email',
        'site'];
    public function getProperties(){
        return $this->properties;
    }
}