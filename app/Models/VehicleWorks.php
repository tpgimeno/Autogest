<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Description of VehicleWorks
 *
 * @author tonyl
 */
class VehicleWorks extends Model 
{
    protected $table = "vehicleWorks";
    protected $properties = ['vehicle_id', 'work_id', 'cantity', 'pvp'];
    public function getProperties(){
        return $this->properties;
    }
}
