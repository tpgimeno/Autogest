<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Services\Buys;

use App\Models\VehiclesType;

/**
 * Description of VehicleService
 *
 * @author tonyl
 */
class VehicleTypeService 
{
    public function deleteVehicleType($id)
    {
        $vehicle = VehiclesType::find($id)->first();
        $vehicle->delete();
    }
}