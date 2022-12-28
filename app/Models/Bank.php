<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Description of Bank
 *
 * @author tonyl
 */
class Bank extends Model
{
    use SoftDeletes;
    protected $table = 'banks';
    protected $properties = ['name',
        'bankCode', 
        'fiscalName',         
        'fiscalId',         
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
