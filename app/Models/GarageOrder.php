<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GarageOrder extends Model 
{
    use SoftDeletes;

    protected $table = "garage_orders";
    protected $properties = ['data' => ['orderNumber',
        'customer_id',                
        'inDate', 
        'outDate', 
        'vehicle_id',
        'plate',
        'brand',
        'model',        
        'inKm',
        'outKm',
        'description',
        'text'],
        'detail' => ['baseOrder',
        'discountOrder',
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