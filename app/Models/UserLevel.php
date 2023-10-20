<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserLevel extends Model {

    protected $table = 'userlevels';
    protected $properties = ['name'];

    public function getProperties() {
        return $this->properties;
    }
    public function list(): BelongToMany{
        return $this->belongsToMany('App\User');
  }
}
