<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use SoftDeletes;
    protected $table = "company";
    protected $properties = ['name', 
        'fiscalId', 
        'fiscalName', 
        'address',
        'postalCode',
        'city',         
        'state', 
        'country', 
        'phone',
        'email',
        'site'];
    
    public function getProperties(){
        return $this->properties;
    }
}