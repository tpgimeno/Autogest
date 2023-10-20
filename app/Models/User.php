<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Model 
{
  use SoftDeletes;

  protected $table = 'users';
  protected $properties = ['name',
      'email',
      'password',
      'address',
      'postalCode',
      'city',
      'state',
      'country',
      'phone',
      'access'];
  
  public function getProperties(){
      return $this->properties;
  }
  
 
}
