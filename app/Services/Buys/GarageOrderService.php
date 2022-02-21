<?php

namespace App\Services\Buys;

use App\Models\GarageOrder;


class GarageOrderService
{
    public function deleteGarageOrder($id)
    {
        
        $garageOrder = GarageOrder::find($id)->first();
        $garageOrder->delete();       

    }
    public function getLastId()
    {
        $lastGarage = GarageOrder::All()->last();
        if($lastGarage)
        {
            return $lastGarage['id'];
        }
        else
        {
            return 1;
        }
        
    }
}