<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Services\Vehicle;

use App\Models\Vehicle;
use App\Models\VehicleAccesories;
use App\Models\VehicleComponents;
use App\Models\VehicleSupplies;
use App\Models\VehicleWorks;
use App\Services\BaseService;
use Illuminate\Database\Capsule\Manager as DB;

/**
 * Description of VehicleService
 *
 * @author tonyl
 */
class VehicleService extends BaseService {

    public function list() {
        $values = Vehicle::join('brands', 'vehicles.brand_id', '=', 'brands.id')
                        ->join('models', 'vehicles.model_id', '=', 'models.id')
                        ->get(['vehicles.id', 'brands.name as brand_id', 'models.name as model_id', 'vehicles.km', 'vehicles.pvp'])->toArray();
        return $values;
    }

    public function getVehicleAccesories($request) {
        $accesories = null;
        if ($request->getMethod() == 'POST') {
            $postData = $request->getParsedBody();
            if ($postData['id']) {
                $accesories = DB::table('vehicleaccesories')
                        ->join('accesories', 'vehicleaccesories.accesory_id', '=', 'accesories.id')
                        ->select('accesories.id', 'vehicleaccesories.vehicle_id', 'accesories.keyString', 'accesories.name')
                        ->where('vehicleaccesories.vehicle_id', '=', intval($postData['id']))
                        ->get();
            }
        } else {
            $params = $request->getQueryParams();
            $accesories = DB::table('vehicleaccesories')
                    ->join('accesories', 'vehicleaccesories.accesory_id', '=', 'accesories.id')
                    ->select('accesories.id', 'vehicleaccesories.vehicle_id', 'accesories.keyString', 'accesories.name')
                    ->where('vehicleaccesories.vehicle_id', '=', intval($params['id']))
                    ->get();
        }
        return $accesories;
    }

    public function getVehicleAccesoriesAjax($vehicle_id) {
        $accesories = DB::table('vehicleaccesories')
                        ->join('accesories', 'vehicleaccesories.accesory_id', '=', 'accesories.id')
                        ->select('accesories.id', 'vehicleaccesories.vehicle_id', 'accesories.keyString', 'accesories.name')
                        ->where('vehicleaccesories.vehicle_id', '=', intval($vehicle_id))
                        ->get()->toArray();
        return $accesories;
    }

    public function addVehicleAccesoryAjax($postData) {
        $responseMessage = null;
        $vehicle_accesory = new VehicleAccesories();
        if (isset($postData['vehicle_id']) && isset($postData['accesory_id'])) {
            $vehicle_accesory->vehicle_id = $postData['vehicle_id'];
            $vehicle_accesory->accesory_id = $postData['accesory_id'];
            $vehicle_accesory->save();
            $responseMessage = "Accesory Saved";
        }
        return $responseMessage;
    }

    public function delVehicleAccesoryAjax($postData) {
        $responseMessage = null;
        $vehicle_accesory = VehicleAccesories::where('vehicle_id', '=', $postData['vehicle_id'])
                        ->where('accesory_id', '=', $postData['accesory_id'])
                        ->get()->first();
        if ($vehicle_accesory) {
            $vehicle_accesory->delete();
            $responseMessage = "Accesory Deleted";
        }
        return $responseMessage;
    }

    public function getVehicleComponents($request) {
        if ($request->getMethod() === 'POST') {
            $postData = $request->getParsedBody();
            $components = DB::table('vehiclecomponents')
                    ->join('vehicles', 'vehiclecomponents.vehicle_id', '=', 'vehicles.id')
                    ->join('components', 'vehiclecomponents.component_id', '=', 'components.id')
                    ->join('maders', 'components.mader_id', '=', 'maders.id')
                    ->select('vehiclecomponents.id', 'maders.name as mader', 'components.ref as ref', 'components.name as name', 'components.pvp as pvp', 'vehiclecomponents.cantity as cantity')
                    ->where('vehiclecomponents.vehicle_id', '=', $postData['vehicle_id'])
                    ->get();
        } else {
            $params = $request->getQueryParams();
            $components = DB::table('vehiclecomponents')
                    ->join('vehicles', 'vehiclecomponents.vehicle_id', '=', 'vehicles.id')
                    ->join('components', 'vehiclecomponents.component_id', '=', 'components.id')
                    ->join('maders', 'components.mader_id', '=', 'maders.id')
                    ->select('vehiclecomponents.id', 'maders.name as mader', 'components.ref as ref', 'components.name as name', 'components.pvp as pvp', 'vehiclecomponents.cantity as cantity')
                    ->where('vehiclecomponents.vehicle_id', '=', $params['id'])
                    ->get();
        }
        return $components;
    }

    public function addVehicleComponentAjax($postData) { 
        $component_exist = true;
        $responseMessage = null;
        $component = VehicleComponents::where('vehicle_id', '=', $postData['vehicle_id'])
                        ->where('component_id', '=', $postData['component_id'])
                        ->get()->first();
        if (!$component) {
            $component = new VehicleComponents();
            $component_exist = false;
        }
       
        $component->component_id = $postData['component_id'];
        $component->vehicle_id = $postData['vehicle_id'];
        $component->cantity = $postData['cantity'];
        $component->pvp = $postData['pvp'];        
        if ($component_exist === true) {
            $component->update();
            $responseMessage = "Componente Actualizado";
        } else {
            $component->save();
            $responseMessage = "Componente Añadido";
        }
        return $responseMessage;
    }
    
    public function delVehicleComponentAjax($postData){
        $component = VehicleComponents::where('id', '=', $postData['id'])                
                ->get()->first();
        $component->delete();
        $responseMessage = "Componente Eliminado";
        return $responseMessage;
    }

    public function getVehicleSupplies($request) {
        if ($request->getMethod() === 'POST') {
            $postData = $request->getParsedBody();
            $supplies = DB::table('vehiclesupplies')
                    ->join('vehicles', 'vehiclesupplies.vehicle_id', '=', 'vehicles.id')
                    ->join('supplies', 'vehiclesupplies.supply_id', '=', 'supplies.id')
                    ->join('maders', 'supplies.mader_id', '=', 'maders.id')
                    ->select('vehiclesupplies.id', 'maders.name as mader', 'supplies.ref as ref', 'supplies.name as name', 'supplies.pvp as pvp', 'vehiclesupplies.cantity as cantity')
                    ->where('vehiclesupplies.vehicle_id', '=', $postData['vehicle_id'])
                    ->get();
        } else {
            $params = $request->getQueryParams();
            $supplies = DB::table('vehiclesupplies')
                    ->join('vehicles', 'vehiclesupplies.vehicle_id', '=', 'vehicles.id')
                    ->join('supplies', 'vehiclesupplies.supply_id', '=', 'supplies.id')
                    ->join('maders', 'supplies.mader_id', '=', 'maders.id')
                    ->select('vehiclesupplies.id', 'maders.name as mader', 'supplies.ref as ref', 'supplies.name as name', 'supplies.pvp as pvp', 'vehiclesupplies.cantity as cantity')
                    ->where('vehiclesupplies.vehicle_id', '=', $params['id'])
                    ->get();
        }
        return $supplies;
    }
    
    public function addVehicleSupplyAjax($postData) {
        
        $supply_exist = true;
        $responseMessage = null;
        $supply = VehicleSupplies::where('vehicle_id', '=', $postData['vehicle_id'])
                        ->where('supply_id', '=', $postData['supply_id'])
                        ->get()->first();
        if (!$supply) {
            $supply = new VehicleSupplies();
            $supply_exist = false;
        }
       
        $supply->supply_id = $postData['supply_id'];
        $supply->vehicle_id = $postData['vehicle_id'];
        $supply->cantity = $postData['cantity'];
        $supply->pvp = $postData['pvp'];        
        if ($supply_exist === true) {
            $supply->update();
            $responseMessage = "Recambio Actualizado";
        } else {
            $supply->save();
            $responseMessage = "Recambio Añadido";
        }
        return $responseMessage;
    }
    
    public function delVehicleSupplyAjax($postData){
        
        $supply = VehicleSupplies::where('id', '=', $postData['id'])                
                ->get()->first();
        $supply->delete();
        $responseMessage = "Recambio Eliminado";
        return $responseMessage;
    }
    
    public function getVehicleWorks($request) {
        if ($request->getMethod() === 'POST') {
            $postData = $request->getParsedBody();
            $works = DB::table('vehicleworks')
                    ->join('vehicles', 'vehicleworks.vehicle_id', '=', 'vehicles.id')
                    ->join('works', 'vehicleworks.work_id', '=', 'works.id')
                    ->select('vehicleworks.id', 'works.reference as reference', 'works.description as description', 'works.pvp as pvp', 'vehicleworks.cantity as cantity')
                    ->where('vehicleworks.vehicle_id', '=', $postData['vehicle_id'])
                    ->get();
        } else {
            $params = $request->getQueryParams();
            $works = DB::table('vehicleworks')
                    ->join('vehicles', 'vehicleworks.vehicle_id', '=', 'vehicles.id')
                    ->join('works', 'vehicleworks.work_id', '=', 'works.id')
                    ->select('vehicleworks.id', 'works.reference as reference', 'works.description as description', 'works.pvp as pvp', 'vehicleworks.cantity as cantity')
                    ->where('vehicleworks.vehicle_id', '=', $params['id'])
                    ->get();
        }
        return $works;
    }
    
    public function addVehicleWorkAjax($postData) {  
        $work_exist = true;
        $responseMessage = null;        
        $work = VehicleWorks::where('vehicle_id', '=', intval($postData['vehicle_id']))
                ->where('work_id', '=', intval($postData['work_id']))
                ->get()->first();
        
        if (!$work) {
            $work = new VehicleWorks();            
            $work_exist = false;
        }
        $work->work_id = $postData['work_id'];
        $work->vehicle_id = $postData['vehicle_id'];
        $work->cantity = $postData['cantity'];
        $work->pvp = $postData['pvp'];        
        if ($work_exist === true) {
            $work->update();
            $responseMessage = "Trabajo Actualizado";
        } else {
            $work->save();
            $responseMessage = "Trabajo Añadido";
        }
        return $responseMessage;
    }
    
    public function delVehicleWorkAjax($postData){
        $work = VehicleWorks::where('id', '=', $postData['id'])                
                ->get()->first();
        $work->delete();
        $responseMessage = "Trabajo Eliminado";
        return $responseMessage;
    }

    public function tofloat($num) {
        $dotPos = strrpos($num, '.');
        $commaPos = strrpos($num, ',');
        $sep = (($dotPos > $commaPos) && $dotPos) ? $dotPos :
                ((($commaPos > $dotPos) && $commaPos) ? $commaPos : false);
        if (!$sep) {
            $result = floatval(preg_replace("/[^0-9]/", "", $num));
        } else {
            $result = floatval(
                    preg_replace("/[^0-9]/", "", substr($num, 0, $sep)) . '.' .
                    preg_replace("/[^0-9]/", "", substr($num, $sep + 1, strlen($num))));
        }
        return $result;
    }

}
