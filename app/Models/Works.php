<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Works extends Model
{
    use SoftDeletes;
    protected $table = "works";
    protected $properties = ['ref', 'name', 'observations', 'pvc', 'pvp'];
    public function getProperties(){
        return $this->properties;
    }
}
