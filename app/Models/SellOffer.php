<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SellOffer extends Model
{
    use SoftDeletes;
    protected $table = 'selloffers';
    protected $properties = ['data' => ['offerNumber', 
        'offerDate', 
        'taxes_id', 
        'paymentWay_id',        
        'customer_id',
        'pvp',
        'discount',
        'tva',
        'total',
        'observations',
        'texts'],
        'vehicle' => ['vehicle_id',
        'brand',
        'model',
        'plate',
        'vin',
        'km',
        'vehiclePvp', 
        'vehicleDiscount',
        'vehicleTva', 
        'vehicleTotal',            
        'vehicleComments'],        
        'components' => [],
        'supplies' => [],
        'works' => []];
    public function getProperties(){
        return $this->properties;
    }
}
        