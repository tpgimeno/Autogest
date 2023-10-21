<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Components extends Model
{
    use SoftDeletes;

    protected $table = "components";
    protected $properties = ['ref', 'mader_id', 'serialNumber', 'name', 'observations', 'pvc', 'pvp'];
    public function getProperties(){
        return $this->properties;
    }

}