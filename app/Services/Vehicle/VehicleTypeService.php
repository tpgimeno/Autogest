<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Services\Vehicle;

use App\Models\VehicleTypes;

/**
 * Description of VehicleService
 *
 * @author tonyl
 */
class VehicleTypeService 
{
    public function deleteVehicleType($id)
    {
        $vehicle = VehicleTypes::find($id)->first();
        $vehicle->delete();
    }
}