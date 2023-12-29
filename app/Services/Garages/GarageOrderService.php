<?php

namespace App\Services\Garages;

use App\Models\GarageOrder;
use App\Models\GarageOrderComponent;
use App\Models\GarageOrderSupply;
use App\Models\GarageOrderWork;
use App\Models\Vehicle;
use App\Services\BaseService;


class GarageOrderService extends BaseService
{
    public function list(){
        $values = GarageOrder::join('vehicles', 'garage_orders.vehicle_id', '=', 'vehicles.id')
                ->join('customers', 'garage_orders.customer_id', '=', 'customers.id')
                ->join('brands', 'vehicles.brand_id', '=', 'brands.id')
                ->join('models', 'vehicles.model_id', '=', 'models.id')
                ->get(['garage_orders.id as id', 'customers.name as customer', 'brands.name as brand', 'models.name as model', 'vehicles.plate as plate', 'customers.phone as phone'])
                ->toArray();
        return $values;
    }
    public function getOrderVehicles(){
        $vehicles = Vehicle::join('brands', 'vehicles.brand_id', '=', 'brands.id')
                ->join('models', 'vehicles.model_id', '=', 'models.id')
                ->select('vehicles.id', 'vehicles.plate', 'vehicles.vin', 'vehicles.km' , 'vehicles.brand_id', 'brands.name as brand', 'vehicles.model_id', 'models.name as model', 'vehicles.vehiclePvp')
                ->get()->toArray();
        return $vehicles;
    }
    
    public function getGarageOrderComponents($request){
        $components = null;
        if ($request->getMethod() == 'POST') {
            $postData = $request->getParsedBody();
            if(isset($postData['id'])){
                $components = GarageOrderComponent::join('components', 'orderComponents.component_id', '=', 'components.id')
                        ->join('maders', 'components.mader_id', '=', 'maders.id')
                        ->where('orderComponents.garageOrder_id', '=', intval($postData['id']))
                        ->select('components.id as component_id', 'orderComponents.id as garageOrdercomponent_id', 'maders.name as mader', 'components.ref as ref', 'components.name as name', 'orderComponents.pvp', 'orderComponents.cantity')
                        ->get();
            }
        }else{
            $params = $request->getQueryParams();
            if(isset($params['id'])){
                $components = GarageOrderComponent::join('components', 'orderComponents.component_id', '=', 'components.id')
                        ->join('maders', 'components.mader_id', '=', 'maders.id')
                        ->where('orderComponents.garageOrder_id', '=', intval($params['id']))
                        ->select('components.id as component_id', 'orderComponents.id as garageOrdercomponent_id', 'maders.name as mader', 'components.ref as ref', 'components.name as name', 'orderComponents.pvp', 'orderComponents.cantity')
                        ->get();
            }
        }
        return $components;
    }
    
    public function addComponentsGarageOrdersAction($postData){ 
//        var_dump($postData);die();
        $component_exist = true;        
        $component = GarageOrderComponent::where('garageOrder_id', '=', $postData['garageOrder_id'])                
                        ->where('component_id', '=', $postData['component_id'])
                        ->get()->first();
        if (!$component) {
            $component = new GarageOrderComponent();
            $component_exist = false;
        }
       
        $component->component_id = $postData['component_id'];
        $component->garageOrder_id = $postData['garageOrder_id'];
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
    
    public function delComponentsGarageOrdersAction($postData){
        $component = GarageOrderComponent::where('id', '=', $postData['id'])
                ->get()->first();
        $component->delete();
        $responseMessage = "Componente Eliminado";
        return $responseMessage;
    }
    
    public function getGarageOrderSupplies($request){
        $supplies = null;
        if ($request->getMethod() == 'POST') {
            $postData = $request->getParsedBody();
            if(isset($postData['id'])){
                $supplies = GarageOrderSupply::join('supplies', 'orderSupplies.supply_id', '=', 'supplies.id')
                        ->join('maders', 'supplies.mader_id', '=', 'maders.id')
                        ->where('orderSupplies.garageOrder_id', '=', intval($postData['id']))
                        ->select('supplies.id as supply_id', 'orderSupplies.id as garageOrdersupply_id', 'maders.name as mader', 'supplies.ref as ref', 'supplies.name as name', 'orderSupplies.pvp', 'orderSupplies.cantity')
                        ->get();
            }
        }else{
            $params = $request->getQueryParams();
            if(isset($params['id'])){
                $supplies = GarageOrderSupply::join('supplies', 'orderSupplies.supply_id', '=', 'supplies.id')
                        ->join('maders', 'supplies.mader_id', '=', 'maders.id')
                        ->where('orderSupplies.garageOrder_id', '=', intval($params['id']))
                        ->select('supplies.id as supply_id', 'orderSupplies.id as garageOrdersupply_id', 'maders.name as mader', 'supplies.ref as ref', 'supplies.name as name', 'orderSupplies.pvp', 'orderSupplies.cantity')
                        ->get();
            }
        }
        return $supplies;
    }
    
    public function addSuppliesGarageOrdersAction($postData){
//        var_dump($postData);  
        $supply_exist = true;        
        $supply = GarageOrderSupply::where('garageOrder_id', '=', $postData['garageOrder_id'])
                        ->where('supply_id', '=', $postData['supply_id'])
                        ->get()->first();
        if (!$supply) {
            $supply = new GarageOrderSupply();
            $supply_exist = false;
        }
       
        $supply->supply_id = $postData['supply_id'];
        $supply->garageOrder_id = $postData['garageOrder_id'];
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
    
    public function delSuppliesGarageOrdersAction($postData){
    
        $supply = GarageOrderSupply::where('id', '=', $postData['id'])
                ->get()->first();
        $supply->delete();
        $responseMessage = "Recambio Eliminado";
        return $responseMessage;
    }
    
    public function getGarageOrderWorks($request){
        $works = null;
        if ($request->getMethod() == 'POST') {
            $postData = $request->getParsedBody();
            if(isset($postData['id'])){
                $works = GarageOrderWork::join('works', 'orderWorks.work_id', '=', 'works.id')                        
                        ->where('orderWorks.garageOrder_id', '=', intval($postData['id']))
                        ->select('works.id as work_id', 'orderWorks.id as garageOrderwork_id', 'works.ref as ref', 'works.name as name', 'orderWorks.pvp', 'orderWorks.cantity')
                        ->get();
            }
        }else{
            $params = $request->getQueryParams();
            if(isset($params['id'])){
                $works = GarageOrderWork::join('works', 'orderWorks.work_id', '=', 'works.id')                       
                        ->where('orderWorks.garageOrder_id', '=', intval($params['id']))
                        ->select('works.id as work_id', 'orderWorks.id as garageOrderwork_id', 'works.ref as ref', 'works.name as name', 'orderWorks.pvp', 'orderWorks.cantity')
                        ->get();
            }
        }
        return $works;
    }
    
    public function addWorksGarageOrdersAction($postData){
//        var_dump($postData);  
        $work_exist = true;        
        $work = GarageOrderWork::where('garageOrder_id', '=', $postData['garageOrder_id'])
                        ->where('work_id', '=', $postData['work_id'])
                        ->get()->first();
        if (!$work) {
            $work = new GarageOrderWork();
            $work_exist = false;
        }
       
        $work->work_id = $postData['work_id'];
        $work->garageOrder_id = $postData['garageOrder_id'];
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
    
    public function delWorksGarageOrdersAction($postData){    
        $work = GarageOrderWork::where('id', '=', $postData['id'])
                ->get()->first();
        $work->delete();
        $responseMessage = "Trabajo Eliminado";
        return $responseMessage;
    }
    
    public function getLastOrderNumber(){
        $lastOrderNumber = GarageOrder::select('garage_orders.orderNumber')
                ->get()->last();
        return $lastOrderNumber;        
    }
    
}