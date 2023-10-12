<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Location extends Model 
{
    use SoftDeletes;

    protected $table = "locations";
    protected $properties = ['store_id', 'name'];
    public function getProperties(){
        return $this->properties;
    }
    public function list(): BelongsToMany{
        return $this->belongsToMany('App\Store');
    }
}