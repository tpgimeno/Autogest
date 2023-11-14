<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Services\Vehicle;

use App\Models\Accesories;
use App\Models\Components;
use App\Models\Supplies;
use App\Models\Vehicle;
use App\Models\VehicleAccesories;
use App\Models\VehicleComponents;
use App\Models\VehicleSupplies;
use App\Services\BaseService;
use Exception;
use Illuminate\Database\Capsule\Manager as DB;
use function GuzzleHttp\json_decode;

/**
 * Description of VehicleService
 *
 * @author tonyl
 */
class VehicleService extends BaseService {
    
    public function list(){
        $values = Vehicle::join('brands', 'vehicles.brand_id', '=', 'brands.id')
                ->join('models', 'vehicles.model_id', '=', 'models.id')
                ->get(['vehicles.id', 'brands.name as brand_id', 'models.name as model_id', 'vehicles.km', 'vehicles.pvp'])->toArray();
        return $values;
    }
    
    public function getVehicleAccesories($request){
         $accesories = null;         
         if($request->getMethod() == 'POST') {
            $postData = $request->getParsedBody();
            if($postData['id']){
                $accesories = DB::table('vehicleaccesories')
                       ->join('accesories', 'vehicleaccesories.accesory_id', '=', 'accesories.id')
                       ->select('accesories.id', 'vehicleaccesories.vehicle_id', 'accesories.keyString', 'accesories.name')
                       ->where('vehicleaccesories.vehicle_id', '=', intval($postData['id']))
                       ->get();
            }
         }else{
            $params = $request->getQueryParams();
            $accesories = DB::table('vehicleaccesories')
                       ->join('accesories', 'vehicleaccesories.accesory_id', '=', 'accesories.id')
                       ->select('accesories.id', 'vehicleaccesories.vehicle_id', 'accesories.keyString', 'accesories.name')
                       ->where('vehicleaccesories.vehicle_id', '=', intval($params['id']))
                       ->get();
         }         
         return $accesories;               
    }
    
    public function getVehicleAccesoriesAjax($vehicle_id){
        $accesories = DB::table('vehicleaccesories')
                       ->join('accesories', 'vehicleaccesories.accesory_id', '=', 'accesories.id')
                       ->select('accesories.id', 'vehicleaccesories.vehicle_id', 'accesories.keyString', 'accesories.name')
                       ->where('vehicleaccesories.vehicle_id', '=', intval($vehicle_id))
                       ->get()->toArray();
        return $accesories;
    }
    
    public function addVehicleAccesoryAjax($postData){
        $responseMessage = null;
        $vehicle_accesory = new VehicleAccesories();        
         if(isset($postData['vehicle_id']) && isset($postData['accesory_id'])){
            $vehicle_accesory->vehicle_id = $postData['vehicle_id'];
            $vehicle_accesory->accesory_id = $postData['accesory_id'];
            $vehicle_accesory->save();
            $responseMessage = "Accesory Saved";          
        }
        return $responseMessage;
    }
    
    public function delVehicleAccesoryAjax($postData){
        $responseMessage = null;        
        $vehicle_accesory = VehicleAccesories::where('vehicle_id', '=', $postData['vehicle_id'])
                ->where('accesory_id', '=', $postData['accesory_id'])
                ->get()->first();
        if($vehicle_accesory){
            $vehicle_accesory->delete();
            $responseMessage = "Accesory Deleted";
        }       
        return $responseMessage;
    }
    public function getVehicleComponents($request){
        if($request->getMethod() === 'POST'){
            $postData = $request->getParsedBody();
            $components = DB::table('vehiclecomponents')
                    ->join('components', 'vehiclecomponents.component_id', '=', 'components.id')
                    ->join('vehicles', 'vehiclecomponents.vehicle_id', '=', 'vehicles.id')
                    ->join('maders', 'components.mader_id', '=', 'maders.id')
                    ->select('vehiclecomponents.id', 'maders.name as mader', 'components.ref as ref','components.name as name', 'components.pvp as pvp')
                    ->where('vehiclecomponents.vehicle_id', '=', $postData['id'])
                    ->get();
        }else{
            $params = $request->getQueryParams();
            $components = DB::table('vehiclecomponents')
                    ->join('components', 'vehiclecomponents.component_id', '=', 'components.id')
                    ->join('vehicles', 'vehiclecomponents.vehicle_id', '=', 'vehicles.id')
                    ->join('maders', 'components.mader_id', '=', 'maders.id')
                    ->select('vehiclecomponents.id', 'maders.name as mader', 'components.ref as ref','components.name as name', 'components.pvp as pvp')
                    ->where('vehiclecomponents.vehicle_id', '=', $params['id'])
                    ->get();
        }
        
        return $components;
    } 
    
    public function getVehicleSupplies($vehicle){
       $selectedSupplies = null;
       if($vehicle){
           $selectedSupplies = DB::table('vehicleSupplies')
                   ->join('supplies', 'vehicleSupplies.supplyId', '=', 'supplies.id')
                   ->select('vehicleSupplies.supplyId', 'supplies.name', 'supplies.ref', 'supplies.mader', 'supplies.pvp')
                   ->where('vehicleSupplies.vehicleId', '=', $vehicle->id)                    
                   ->get();
       }
       return $selectedSupplies;
    }
    public function findSupply($data){
        $supply = VehicleSupplies::where('supplyId', '=', intval($data->supplyId))
                ->where('vehicleId', '=', intval($data->id))
                ->first();
        return $supply;
    }
    public function searchSupply($searchString){                
        if($searchString == null){
            $supplies = Supplies::All();
        }
        else{
            $supplies = DB::table('supplies')                 
                 ->select('supplies.id', 'supplies.ref', 'supplies.serialNumber', 'supplies.pvp')
                 ->where('supplies.id', 'like', "%".$searchString."$")
                 ->orWhere('supplies.ref', 'like', "$".$searchString."$")
                 ->orWhere('maders.name', 'like', "$".$searchString."$")
                 ->orWhere('supplies.serialNumber', 'like', "$".$searchString."$")
                 ->whereNull('deleted_at')
                 ->get();
        }
        return $supplies;
    }
    public function addVehicleSupply($array){
        $data = json_decode($array['supply']);        
        $vehicleSupply = new VehicleSupplies();                  
        $vehicleSupply->supplyId = $data->supplyId;
        $vehicleSupply->vehicleId = $data->id;
        $vehicleSupply->cantity = $data->cantity;
        $vehicleSupply->price = $data->price;
        if($this->findSupply($data)){
            $vehicleSupply->id = $this->findSupply($data)->id;
            $vehicleSupply->update();
            $responseMessage = 'Supply Updated';
        }
        else{
            $vehicleSupply->save();
            $responseMessage = 'Supply Saved';
        }
        return $responseMessage;
    }
    public function getSelectedSupply($array){
        $selectedSupply = null;
        if(isset($array['supplyId'])){
            $selectedSupply = DB::table('supplies')                    
                    ->select('supplies.id', 'supplies.name', 'supplies.ref', 'supplies.pvp')
                    ->where('supplies.id', '=', intval($array['supplyId']))
                    ->whereNull('supplies.deleted_at')
                    ->get()->first();
        }
        return $selectedSupply;
    }
    public function deleteVehicleSupply($array){
        $supply = VehicleSupplies::where('supplyId', '=', $array['supplyId'])
                ->where('vehicleId', '=', $array['id'])
                ->first();        
        if($supply) {
            $supply->delete();
        }        
    }
    public function getSupplyPrice($array, $supply){
        $supplyPrice = null;
        if(isset($array['supplyId'])&& isset($array['price'])){
            $supplyPrice = floatval($array['price']);
        }else if($supply){
            $supplyPrice = $supply->pvp;
        }
        return $supplyPrice;
    }
    public function getSupplyCantity($array){
        $supplyCantity = null;
        if(isset($array['supplyId']) && isset($array['cantity'])){
            $supplyCantity = intval($array['cantity']);
        }else{
            $supplyCantity = 0;
        }
        return $supplyCantity;
    }
    public function setVehicle($array){
        $vehicle = null;       
        if(isset($array['id'])){
            $vehicle = Vehicle::find(intval($array['id']));                    
        }
        return $vehicle;
    }   
    
    
    public function tofloat($num) {
        $dotPos = strrpos($num, '.');
        $commaPos = strrpos($num, ',');
        $sep = (($dotPos > $commaPos) && $dotPos) ? $dotPos :
            ((($commaPos > $dotPos) && $commaPos) ? $commaPos : false);
        if (!$sep) {
            $result = floatval(preg_replace("/[^0-9]/", "", $num));
        }else{
            $result = floatval(
                preg_replace("/[^0-9]/", "", substr($num, 0, $sep)) . '.' .
                preg_replace("/[^0-9]/", "", substr($num, $sep+1, strlen($num))));
        }
        return $result;
    } 
}
