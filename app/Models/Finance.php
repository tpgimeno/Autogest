<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Finance extends Model {
    use SoftDeletes;
    protected $table = "finance";
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
        'site',
        'bank_id'];
    public function getProperties(){
        return $this->properties;
    }
}