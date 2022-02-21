<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentWays extends Model 
{
    use SoftDeletes;

    protected $table = "paymentWays";
    protected $properties = ['name', 'account', 'discount'];
    public function getProperties(){
        return $this->properties;
    }
}