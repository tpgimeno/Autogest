<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model {
    use SoftDeletes;
    protected $table = "vehicles"; 
    protected $properties = ['data' => ['brand_id',
        'model_id',
        'description',
        'plate',
        'registryDate',
        'vin',
        'store_id',
        'location',
        'type_id',
        'color',
        'places',
        'doors',
        'power',
        'km'],
        'detail' => ['providor_id',
        'cost',
        'pvp',
        'transference',
        'service',
        'secondKey',
        'rebu',
        'arrival',
        'buyDate',
        'sellDate',
        'appointDate',        
        'customer_id',
        'seller_id'],
        'technical' => ['dataType',
        'variant',
        'version',
        'comercialName',
        'mma',
        'mmaAxe1',
        'mmaAxe2',
        'mmac',
        'mmar',
        'mmarf',
        'mom',
        'momAxe1',
        'momAxe2',
        'large',
        'width',
        'height',
        'frontOverhang',
        'rearOverhang',
        'axeDistance',
        'chargeLength',
        'deposit',
        'initCharge'],
        'accesories' => [],
        'components' => [],
        'supplies' => [],
        'works' => []];
    
    public function getProperties(){
      return $this->properties;
    }
}