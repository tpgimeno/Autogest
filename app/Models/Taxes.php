<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Taxes extends Model 
{
    protected $table = "taxes";
    protected $properties = ['name', 'percentaje'];
    
    public function getProperties(){
        return $this->properties;
    }
}