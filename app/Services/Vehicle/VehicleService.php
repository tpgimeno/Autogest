<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Services\Vehicle;


use App\Models\Vehicle;

/**
 * Description of VehicleService
 *
 * @author tonyl
 */
class VehicleService 
{
    public function deleteVehicle($id)
    {
        $vehicle = Vehicle::find($id)->first();
        $vehicle->delete();
    }
}
