<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Accesories extends Model 
{
    protected $table = "accesories";
    protected $properties = ['name', 'keyString'];
    public function getProperties(){
        return $this->properties;
    }
}