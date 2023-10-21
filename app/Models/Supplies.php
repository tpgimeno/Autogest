<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplies extends Model
{
    use SoftDeletes;
    protected $table = "supplies";
    protected $properties = ['ref', 'mader_id', 'maderCode', 'name', 'observations', 'stock', 'pvc', 'pvp'];
    public function getProperties(){
        return $this->properties;
    }

}