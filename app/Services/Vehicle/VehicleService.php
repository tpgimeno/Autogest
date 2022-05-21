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
                ->select('models.id', 'models.brandId as filter', 'models.name as iter')
                ->whereNull('models.deleted_at')
                ->get();
        return $models;
    }
    public function reloadModels($array){
        $models = DB::table('models')
                ->join('brands', 'models.brandId', '=', 'brands.id')
                ->select('models.id', 'models.name as iter')
                ->where('brands.name', 'like', "%".$array['brand']."%")
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
                ->select('locations.id', 'locations.storeId as filter', 'locations.name as iter')
                ->whereNull('locations.deleted_at')
                ->get();
        return $locations;
    }
    public function reloadLocations($array){
        $locations = DB::table('locations')
                ->join('stores', 'locations.storeId', '=', 'stores.id')
                ->select('locations.id', 'locations.name as iter')
                ->where('stores.name', 'like', "%".$array['store']."%")
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
    public function getSellers(){
        $sellers = DB::table('sellers')
                ->select('sellers.id', 'sellers.name as iter')
                ->whereNull('sellers.deleted_at')
                ->get();
        return $sellers;
    }
    public function getCustomers(){
        $customers = DB::table('customers')
                ->select('customers.id', 'customers.name as iter')
                ->whereNull('customers.deleted_at')
                ->get();
        return $customers;
    }
    public function getBrandIdByName($array){
        $brand = null;
        if(isset($array['brand']) && $array['brand']){
            $brand = DB::table('brands')
                    ->select('brands.id')
                    ->where('brands.name', 'like', "%".$array['brand']."%")
                    ->get()->first()->id;
        }
        return $brand;
    }
    public function getModelIdByName($array){
        $model = null;
        if(isset($array['model']) && $array['model']){
            $model = DB::table('models')
                    ->select('models.id')
                    ->where('models.name', 'like', "%".$array['model']."%")
                    ->get()->first()->id;
        }
        return $model;
    }
    public function getVehicleTypeIdByName($array){
        $type = null;
        if(isset($array['vehicleType']) && $array['vehicleType']){
           $type = DB::table('vehicletypes')
                ->select('vehicletypes.id')
                ->where('vehicletypes.name', 'like', "%".$array['vehicleType']."%")
                ->get()->first()->id; 
        }        
        return $type;
    }
    public function getStoreIdByName($array){
        $store = null;
        if(isset($array['store']) && $array['store']){
            $store = DB::table('stores')
                    ->select('stores.id')
                    ->where('stores.name', 'like', "%".$array['store']."%")
                    ->get()->first()->id;
        }
        return $store;
    }
    public function getLocationIdByName($array){
        $location = null;
        if(isset($array['location']) && $array['location']){
            $location = DB::table('locations')
                    ->select('locations.id')
                    ->where('locations.name', 'like', "%".$array['location']."%")
                    ->get()->first()->id;
        }
        return $location;
    }
    public function getProvidorIdByName($array){
        $providor = null;
        if(isset($array['providor']) && $array['providor']){
            $providor = DB::table('providers')
                    ->select('providers.id')
                    ->where('providers.name', 'like', "%".$array['providor']."%")
                    ->get()->first()->id;
        }
        return $providor;
    }
    public function getCustomerIdByName($array){
        $customer = null;
        if(isset($array['customer']) && $array['customer']){
            $customer = DB::table('customers')
                    ->select('customers.name')
                    ->where('name', 'like', "%".$array['customer']."%")
                    ->get()->first()->name;
        }
        return $customer;
    }
    public function getSellerIdByName($array){
        $seller = null;
        if(isset($array['seller']) && $array['seller']){
            $seller = DB::table('sellers')
                    ->select('sellers.id')
                    ->where('sellers.name', 'like', "%".$array['seller']."%")
                    ->get()->first()->id;
        }
        return $seller;
    }
    public function getRegistryDateValue($array){
        $registryDate = null;
        if(isset($array['registryDate'])&& $array['registryDate']){
            $registryDate = Date('y-m-d', strtotime($array['registryDate']));
        }
        return $registryDate;
    }
    public function getSecondKeyValue($array){
        if(isset($array['secondKey'])){
            $secondKey = 1;
        }else{
            $secondKey = 0;
        }       
        return $secondKey;
    }
    public function getRebuValue($array){
        if(isset($array['rebu'])){
            $rebu = 1;
        }else{
            $rebu = 0;
        }
        return $rebu;
    }
    public function getTechnicCardValue($array){
        if(isset($array['technicCard'])){
            $technicCard = 1;
        }else{
            $technicCard = 0;
        }
        return $technicCard;
    }
    public function getPermissionValue($array){
        if(isset($array['permission'])){
            $permission = 1;
       }else{
            $permission = 0;
       }
       return $permission;
    }
    public function getArrivalValue($array){
        $arrival = null;
        if(isset($array['arrival'])&& $array['arrival']){
            $arrival = Date('y-m-d', strtotime($array['arrival'])); 
        }
        return $arrival;
    }
    public function getDateBuyValue($array){
         $dateBuy = null;
         if(isset($array['dateBuy'])&& $array['dateBuy']){
             $dateBuy = Date('y-m-d', strtotime($array['dateBuy'])); 
         }    
         return $dateBuy;
    }
    public function getAppointValue($array){
        $appoint = null;
        if(isset($array['appoint'])&& $array['appoint']){
             $appoint = Date('y-m-d', strtotime($array['appoint']));
        }    
        return $appoint;
    }
    public function getSellDateValue($array){
        $sellDate = null;
        if(isset($array['dateSell'])&& $array['dateSell']){
             $sellDate = Date('y-m-d', strtotime($array['appoint']));
        } 
        return $sellDate;
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
        }else if($component){
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
        $component = VehicleComponents::where('componentId', '=', intval($data->componentId))
                ->where('vehicleId', '=', intval($data->id))->get()->first();
        return $component;
    }
    public function addVehicleComponent($array){
        $data = json_decode($array['component']);        
        $vehicleComponent = new VehicleComponents();                   
        $vehicleComponent->componentId = $data->componentId;
        $vehicleComponent->vehicleId = $data->id;
        $vehicleComponent->cantity = $data->cantity;
        $vehicleComponent->pvp = $data->pvp;
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
    public function saveVehicle($array){ 
        if(isset($array['id'])){
            $vehicle = $this->setVehicle($array);
        }
        if(!$vehicle){
            $vehicle = new Vehicle();
        }       
        $vehicle->registryDate = $this->getRegistryDateValue($array);
        $vehicle->plate = $array['plate'];
        $vehicle->vin = $array['vin'];
        $vehicle->brand = $this->getBrandIdByName($array);       
        $vehicle->model = $this->getModelIdByName($array);       
        $vehicle->description = $array['description'];
        $vehicle->type = $this->getVehicleTypeIdByName($array);
        $vehicle->store = $this->getStoreIdByName($array);
        $vehicle->location = $this->getLocationIdByName($array);
        $vehicle->km = intval($array['km']);
        $vehicle->power = intval($array['power']);
        $vehicle->places = intval($array['places']);
        $vehicle->doors = intval($array['doors']);
        $vehicle->providor = $this->getProvidorIdByName($array);       
        $vehicle->arrival = $this->getArrivalValue($array);        
        $vehicle->buyDate = $this->getDateBuyValue($array);         
        $vehicle->transference = $array['transference'];       
        $vehicle->service = $array['service'];       
        $vehicle->secondKey = $this->getSecondKeyValue($array);
        $vehicle->rebu = $this->getRebuValue($array);
        $vehicle->technicCard = $this->getTechnicCardValue($array);
        $vehicle->permission = $this->getPermissionValue($array);
        $vehicle->cost = $this->tofloat($array['cost']);
        $vehicle->pvp = $this->tofloat($array['pvp']);
        $vehicle->sellDate = $this->getSellDateValue($array);
        $vehicle->appointDate = $this->getAppointValue($array); 
        $vehicle->customer = $this->getCustomerIdByName($array);
        $vehicle->seller = $this->getSellerIdByName($array);
        $vehicle->dataType = $array['dataType'];
        $vehicle->variant = $array['variant'];
        $vehicle->version = $array['version'];
        $vehicle->comercialName = $array['comercialName'];
        $vehicle->mma = intval($array['mma']);
        $vehicle->mmaAxe1 = intval($array['mmaAxe1']);
        $vehicle->mmaAxe2 = intval($array['mmaAxe2']);
        $vehicle->mmac = intval($array['mmac']);
        $vehicle->mmar = intval($array['mmar']);
        $vehicle->mmarf = intval($array['mmarf']);
        $vehicle->mom = intval($array['mom']);
        $vehicle->momAxe1 = intval($array['momAxe1']);
        $vehicle->momAxe2 = intval($array['momAxe2']);
        $vehicle->large = intval($array['large']);
        $vehicle->width = intval($array['width']);
        $vehicle->height = intval($array['height']);       
        $vehicle->frontOverhang = intval($array['frontOverhang']);
        $vehicle->rearOverhang = intval($array['rearOverhang']);
        $vehicle->axeDistance = intval($array['axeDistance']);
        $vehicle->chargeLength = intval($array['chargeLength']);
        $vehicle->deposit = intval($array['deposit']);
        $vehicle->initCharge = intval($array['initCharge']);       
        try{
             if($this->setVehicle($array)){                
                 $vehicle->update();
                 $responseMessage = 'Updated';
             }else{
                 $vehicle->save();
                 $responseMessage = 'Saved';
             }
        } catch (Exception $ex) {
             $responseMessage = $ex->getMessage();
        }       
        return $responseMessage;
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
