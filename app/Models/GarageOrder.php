<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GarageOrder extends Model 
{
    use SoftDeletes;

    protected $table = "garage_orders";
    protected $properties = ['data' => ['inDate', 
        'outDate',
        'plate',
        'brand',
        'model',
        'customer_id',
        'inKm',
        'outKm',
        'description',
        'text'],
        'detail' => ['baseOrder',
        'tvaOrder',
        'totalOrder',
        'observations'],
        'components' => [],
        'supplies' => [],
        'works' => []];
    
    public function getProperties(){
        return $this->properties;
    }
}