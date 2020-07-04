<?php

namespace App\Services\Buys;

use App\Models\Garage;


class GarageService
{
    public function deleteGarage($id)
    {
        
        $garage = Garage::find($id)->first();
        $garage->delete();          

    }
}