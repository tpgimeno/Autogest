<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Providor extends Model 
{
    use SoftDeletes;

    protected $table = "providors";
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