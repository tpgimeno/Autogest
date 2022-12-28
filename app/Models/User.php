<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Model 
{
  use SoftDeletes;

  protected $table = 'users';
  protected $properties = ['email', 
      'password'];
  
  public function getProperties(){
      return $this->properties;
  }
  
}
