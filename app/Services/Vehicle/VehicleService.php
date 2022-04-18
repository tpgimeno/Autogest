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

/**
 * Description of VehicleService
 *
 * @author tonyl
 */
class VehicleService extends BaseService {
   public function getVehicles(){
       $vehicles = DB::table('vehicles')  
                ->join('brands', 'vehicles.brand', '=', 'brands.id')
                ->join('models', 'vehicles.model', '=', 'models.id')
                ->select('vehicles.id', 'brands.name as brand', 'models.name as model', 'vehicles.description', 'vehicles.plate', 'vehicles.vin')
                ->orderBy('brand', 'asc')
                ->orderBy('model', 'asc')
                ->orderBy('vehicles.plate', 'asc')                
                ->whereNull('vehicles.deleted_at')                
                ->get(); 
       return $vehicles;
   }
   public function searchVehicles($searchString){
       $vehicles = DB::table('vehicles')
                ->join('brands', 'vehicles.brand', '=', 'brands.id')
                ->join('models', 'vehicles.model', '=', 'models.id')
                ->join('vehicletypes', 'vehicles.type', '=', 'vehicletypes.id')
                ->select('vehicles.id as id', 'vehicles.plate as plate', 'vehicles.vin as vin', 
                        'vehicles.description as description', 'vehicles.location', 'vehicletypes.name as type', 
                        'brands.name as brand', 'models.name as model', 'vehicles.color', 'vehicles.places',
                        'vehicles.doors', 'vehicles.power', 'vehicles.cost', 'vehicles.pvp', 'vehicles.accesories')
                ->orderBy('brand', 'asc')
                ->orderBy('model', 'asc')
                ->orderBy('vehicles.plate', 'asc')  
                ->where("brands.name", "like", "%".$searchString."%" )
                ->orWhere("models.name", "like", "%".$searchString."%")
                ->orWhere("vehicles.description", "like", "%".$searchString."%")
                ->orWhere("vehicles.plate", "like", "%".$searchString."%")
                ->orWhere("vehicles.vin", "like", "%".$searchString."%")
                ->orWhere("vehicletypes.name", "like", "%".$searchString."%")
                ->orWhere("vehicles.id", "like", "%".$searchString."%")
                ->WhereNull('vehicles.deleted_at')
                ->get();     
       return $vehicles;
   }
   public function getBrands(){
       $brands = DB::table('brands')
               ->select('brands.id', 'brands.name as iter')
               ->whereNull('brands.deleted_at')
               ->get();
       return $brands;
   }
   public function getModels(){
       $models = DB::table('models')
               ->join('brands', 'models.brandId', '=', 'brands.id')
               ->select('models.id', 'brands.name', 'models.name as iter')
               ->whereNull('models.deleted_at')
               ->get();
       return $models;
   }
   public function getStores(){
       $stores = DB::table('stores')
               ->select('stores.id', 'stores.name as iter')
               ->whereNull('stores.deleted_at')
               ->get();
       return $stores;
   }
   public function getLocations(){
       $locations = DB::table('locations')
               ->select('locations.id', 'locations.name as iter')
               ->whereNull('locations.deleted_at')
               ->get();
       return $locations;
   }
   public function getTypes(){
       $types = DB::table('vehicletypes')
               ->select('vehicletypes.id', 'vehicletypes.name as iter')
               ->whereNull('vehicletypes.deleted_at')
               ->get();
       return $types;
   }
   public function getProvidors(){
       $providors = DB::table('providers')
               ->select('providers.id', 'providers.name as iter')
               ->whereNull('providers.deleted_at')
               ->get();
       return $providors;
   }
   public function getBrandByName($array){
       $brand = DB::table('brands')
               ->select('brands.id')
               ->where('brands.name', 'like', "%".$array['brand']."%")
               ->get()->first();
       return $brand->id;
   }
   public function getModelByName($array){
       $model = DB::table('models')
               ->select('models.id')
               ->where('models.name', 'like', "%".$array['model']."%")
               ->get()->first();
       return $model->id;
   }
   public function getVehicleTypeByName($array){
       $type = DB::table('vehicletypes')
               ->select('vehicletypes.id')
               ->where('vehicletypes.name', 'like', "%".$array['type']."%")
               ->get()->first();
       return $type->id;
   }
   public function getSelectedTab($array){
        $tab = null;
        if(isset($array['selectedTab'])){
            $tab = $array['selectedTab'];
        }
        return $tab;
   }
   public function getVehicleAccesories($vehicle){
        $accesories = null;
        if($vehicle){
            $accesories = DB::table('vehicleaccesories')
                   ->join('accesories', 'vehicleaccesories.accesoryId', '=', 'accesories.id')
                   ->select('accesories.id', 'vehicleaccesories.vehicleId', 'accesories.keyString', 'accesories.name')
                   ->where('vehicleaccesories.vehicleId', '=', intval($vehicle->id))
                   ->get();
        }
        return $accesories;               
   }
   public function getAccesoryByName($name){
       $accesory = Accesories::where('name', 'like', "%".$name->accesory."%")->first();
       return $accesory;
   }
   public function addVehicleAccesory($getAccesory){
        $accesory = $this->getAccesoryByName($getAccesory);        
        $vehicle_accesory = VehicleAccesories::where('vehicleId', '=', $getAccesory->id)
                ->where('accesoryId', '=', $accesory->id)
                ->get()->first();             
        if($vehicle_accesory === null) {
            $vehicle_accesory = new VehicleAccesories();
        }    
        try{
            $vehicle_accesory->vehicleId = $getAccesory->id;
            $vehicle_accesory->accesoryId = $accesory->id;        
            $vehicle_accesory->save();
            $responseMessage = 'Accesory Saved';
        } catch (Exception $ex) {
            $responseMessage = $ex->getMessage();
        }
        return $responseMessage;
   }
   public function deleteVehicleAccesory($array){      
        $getAccesory = json_decode($array['vhaccesory']);        
        $accesory = Accesories::where('name', 'like', "%".$getAccesory->accesory."%")->first();               
        $vehicle_accesory = VehicleAccesories::where('vehicleId', '=', $getAccesory->id)
                ->where('accesoryId', '=', $accesory->id)
                ->first();
        try{
            $vehicle_accesory->delete();
            $responseMessage = 'Accesory Deleted';
        } catch (Exception $ex) {
            $responseMessage = $ex->getMessage();
        }
        return $responseMessage;                            
   }
   public function getVehicleComponents($vehicle){
        $selectedComponents = null;        
        if($vehicle){
            $selectedComponents = DB::table('vehicleComponents')
                    ->join('components', 'vehicleComponents.componentId', '=', 'components.id')                   
                    ->select('vehicleComponents.componentId', 'vehicleComponents.vehicleId as vehicleId', 'vehicleComponents.cantity as cantity', 'components.name', 'components.ref', 'components.serialNumber', 'components.mader', 'vehicleComponents.pvp')
                    ->where('vehicleComponents.vehicleId', '=', $vehicle->id)                    
                    ->get();                    
        }     
        return $selectedComponents;
    }
    public function getVehicleSupplies($vehicle){
       $selectedSupply = null;
       if($vehicle){
           $selectedSupply = DB::table('vehicleSupplies')
                   ->join('supplies', 'vehicleSupplies.supplyId', '=', 'supplies.id')
                   ->select('vehicleSupplies.supplyId', 'supplies.name', 'supplies.ref', 'supplies.mader', 'supplies.pvp')
                   ->where('vehicleSupplies.vehicleId', '=', $vehicle->id)                    
                   ->get();
       }
       return $selectedSupply;
    }
    public function searchComponent($searchString){
        if($searchString == null){
            $components = Components::All();
        }
        else{
            $components = DB::table('components')
                 ->join('maders', 'components.mader', '=', 'maders.id')
                 ->select('components.id', 'components.ref', 'components.serialNumber', 'components.pvp')
                 ->where('components.id', 'like', "%".$searchString."$")
                 ->orWhere('components.ref', 'like', "$".$searchString."$")
                 ->orWhere('maders.name', 'like', "$".$searchString."$")
                 ->orWhere('components.serialNumber', 'like', "$".$searchString."$")
                 ->whereNull('deleted_at')
                 ->get();
        }
        return $components;
    }
    public function getSelectedComponent($array){        
        $selectedComponent = null;
        if(isset($array['componentId'])){
            $selectedComponent = Components::find(intval($array['componentId']));
        }       
        return $selectedComponent;
    }
    public function getComponentPrice($array, $component){
        $componentPrice = null;
        if(isset($array['componentId'])&& isset($array['price'])){
            $componentPrice = floatval($array['price']);
        }
        if($component){
            $componentPrice = $component->pvp;
        }
        return $componentPrice;
    }
    public function getComponentCantity($array){
        $componentCantity = null;
        if(isset($array['componentId'])&& isset($array['cantity'])){
            $componentCantity = intval($array['cantity']);
        }else{
            $componentCantity = 0;
        }
        return $componentCantity;
    }
    public function findVehicleComponent($data){        
        $component = VehicleComponents::where('componentId', '=', intval($data['componentId']))
                ->where('vehicleId', '=', intval($data['id']))->get()->first();
        return $component;
    }
    public function addVehicleComponent($array){
        $data = json_decode($array['component']);        
        $vehicleComponent = new VehicleComponents();                   
        $vehicleComponent->componentId = $data->componentId;
        $vehicleComponent->vehicleId = $data->id;
        $vehicleComponent->cantity = $data->cantity;
        $vehicleComponent->pvp = $data->price;
        try{
            if($this->findVehicleComponent($data)){
            $vehicleComponent->id = $this->findVehicleComponent($data)->id;
            $vehicleComponent->update();
            $responseMessage = 'Component Updated';
            }else{
                $vehicleComponent->save();
                $responseMessage = 'Component Saved';
            }
        } catch (Exception $ex) {
            $responseMessage = $ex->getMessage();
        }
        return $responseMessage;
    }    
    public function deleteVehicleComponent($array){        
        $component = $this->findVehicleComponent($array);               
        if($component) {
            $component->delete();           
        }        
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
    public function getSupplyPrice($array){
        $supplyPrice = null;
        if(isset($array['supplyPrice'])){
            $supplyPrice = floatval($array['supplyPrice']);
        }
        return $supplyPrice;
    }
    public function getSupplyCantity($array){
        $supplyCantity = null;
        if(isset($array['supplyCantity'])){
            $supplyCantity = intval($array['supplyCantity']);
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
}
