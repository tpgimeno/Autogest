<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model {
    use SoftDeletes;
    protected $table = "vehicles";
    protected $properties = ['brand',
        'model',
        'description',
        'plate',
        'registryDate',
        'vin',
        'store',
        'location',
        'type',
        'color',
        'places',
        'doors',
        'power',
        'km',
        'providor',
        'cost',
        'pvp',
        'accesories',
        'transference',
        'service',
        'secondKey',
        'rebu',
        'technicCard',
        'permission',
        'arrival',
        'buyDate',
        'sellDate',
        'appointDate',
        'customer',
        'seller',
        'state',
        'dataType',
        'variant',
        'version',
        'comercialName'];    
    public function getProperties(){
        return $this->properties;
    }
}