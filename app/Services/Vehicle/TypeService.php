<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Services;

use App\Models\VehicleTypes;

/**
 * Description of TypeService
 *
 * @author tonyl
 */
class TypeService {
    public function deleteType($id)
    {        
        $customer = VehicleTypes::find($id)->first();
        $customer->delete();    
    }
}
