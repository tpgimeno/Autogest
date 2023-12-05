<?php

namespace App\Services\Sales;

use App\Models\ModelVh;
use App\Models\SellOffer;
use App\Models\SellOffersComponents;
use App\Models\SellOffersSupplies;
use App\Models\SellOffersWorks;
use App\Models\Vehicle;
use App\Services\BaseService;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class SellOfferService extends BaseService{
    public function list(){
        $offer = SellOffer::join('vehicles', 'selloffers.vehicle_id', '=', 'vehicles.id')                
                ->join('customers', 'selloffers.customer_id', '=', 'customers.id')                
                ->join('brands', 'vehicles.brand_id', '=', 'brands.id')
                ->join('models', 'vehicles.model_id', '=', 'models.id')
                ->select('selloffers.id', 'selloffers.offerNumber', 'selloffers.offerDate', 'customers.name as customer', 'vehicles.plate as plate','brands.name as brand', 'models.name as model', 'selloffers.total')
                ->get()->toArray();
        return $offer;
    }
    
    public function getSellOfferComponents($request){
        $components = null;
        if ($request->getMethod() == 'POST') {
            $postData = $request->getParsedBody();
            if(isset($postData['id'])){
                $components = SellOffersComponents::join('components', 'sellofferscomponents.component_id', '=', 'components.id')
                        ->join('maders', 'components.mader_id', '=', 'maders.id')
                        ->where('sellofferscomponents.selloffer_id', '=', intval($postData['id']))
                        ->select('components.id as component_id', 'sellofferscomponents.id as selloffercomponent_id', 'maders.name as mader', 'components.ref as ref', 'components.name as name', 'sellofferscomponents.pvp', 'sellofferscomponents.cantity')
                        ->get();
            }
        }else{
            $params = $request->getQueryParams();
            if(isset($params['id'])){
                $components = SellOffersComponents::join('components', 'sellofferscomponents.component_id', '=', 'components.id')
                        ->join('maders', 'components.mader_id', '=', 'maders.id')
                        ->where('sellofferscomponents.selloffer_id', '=', intval($params['id']))
                        ->select('components.id as component_id', 'sellofferscomponents.id as selloffercomponent_id', 'maders.name as mader', 'components.ref as ref', 'components.name as name', 'sellofferscomponents.pvp', 'sellofferscomponents.cantity')
                        ->get();
            }
        }
        return $components;
    }
    
    public function getSellOfferSupplies($request){
        $supplies = null;
        if ($request->getMethod() == 'POST') {
            $postData = $request->getParsedBody();
            if(isset($postData['id'])){
                $supplies = SellOffersSupplies::join('supplies', 'sellofferssupplies.supply_id', '=', 'supplies.id')
                        ->join('maders', 'supplies.mader_id', '=', 'maders.id')
                        ->where('sellofferssupplies.selloffer_id', '=', intval($postData['id']))
                        ->select('sellofferssupplies.id as supply_id', 'sellofferssupplies.id as selloffersupply_id', 'maders.name as mader', 'supplies.ref as ref', 'supplies.name as name', 'sellofferssupplies.pvp', 'sellofferssupplies.cantity')
                        ->get();
            }
        }else{
            $params = $request->getQueryParams();
            if(isset($params['id'])){
                $supplies = SellOffersSupplies::join('supplies', 'sellofferssupplies.supply_id', '=', 'supplies.id')
                         ->join('maders', 'supplies.mader_id', '=', 'maders.id')
                        ->where('sellofferssupplies.selloffer_id', '=', intval($params['id']))
                        ->select('sellofferssupplies.id as supply_id', 'sellofferssupplies.id as selloffersupply_id', 'maders.name as mader', 'supplies.ref as ref', 'supplies.name as name', 'sellofferssupplies.pvp', 'sellofferssupplies.cantity')
                        ->get();
            }
        }        
        return $supplies;
    }
    
    public function getSellOfferWorks($request){
        $works = null;
        if ($request->getMethod() == 'POST') {
            $postData = $request->getParsedBody();
            if(isset($postData['id'])){
                $works = SellOffersWorks::join('works', 'selloffersworks.work_id', '=', 'works.id')
                        ->where('selloffersworks.selloffer_id', '=', $postData['id'])
                        ->select('works.id as work_id', 'selloffersworks.id as sellofferwork_id', 'works.reference', 'works.description as description', 'selloffersworks.pvp', 'selloffersworks.cantity')
                        ->get();
            }
        }else{
            $params = $request->getQueryParams();
            if(isset($params['id'])){
                $works = SellOffersWorks::join('works', 'selloffersworks.work_id', '=', 'works.id')
                        ->where('selloffersworks.selloffer_id', '=', $params['id'])
                        ->select('works.id as work_id', 'selloffersworks.id as sellofferwork_id', 'works.reference', 'works.description as description', 'selloffersworks.pvp', 'selloffersworks.cantity')
                        ->get();
            }
        }
        return $works;
    }
    
    public function getSellOfferVehicles(){       
        $vehicles = Vehicle::join('brands', 'vehicles.brand_id', '=', 'brands.id')
                ->join('models', 'vehicles.model_id', '=', 'models.id')
                ->select('vehicles.id', 'vehicles.plate', 'vehicles.vin', 'vehicles.km' , 'vehicles.brand_id', 'brands.name as brand', 'vehicles.model_id', 'models.name as model', 'vehicles.vehiclePvp')
                ->get();
        return $vehicles;
    }
    
    public function getModelsByBrandAjax($brand){
        $models = ModelVh::where('models.brand_id', '=', intval($brand))
                ->get()->toArray();       
        return $models;
    }
    
    public function getVehiclesByModelAjax($brand, $model){
        $vehicles = Vehicle::where('vehicles.brand_id', '=', intval($brand))
                ->where('vehicles.model_id', '=', intval($model))
                ->get();
        
        return $vehicles;
    }
    
    public function saveSellOfferComponentAjax($postData){
//        var_dump($postData);  
        $component_exist = true;        
        $component = SellOffersComponents::where('selloffer_id', '=', $postData['selloffer_id'])
                        ->where('component_id', '=', $postData['component_id'])
                        ->get()->first();
        if (!$component) {
            $component = new SellOffersComponents();
            $component_exist = false;
        }
       
        $component->component_id = $postData['component_id'];
        $component->selloffer_id = $postData['selloffer_id'];
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
    
    public function deleteSellOfferComponentAjax($postData){
    
        $component = SellOffersComponents::where('id', '=', $postData['id'])
                ->get()->first();
        $component->delete();
        $responseMessage = "Componente Eliminado";
        return $responseMessage;
    }
    public function saveSellOfferSupplyAjax($postData){
//        var_dump($postData);  
        $supply_exist = true;        
        $supply = SellOffersSupplies::where('selloffer_id', '=', $postData['selloffer_id'])
                        ->where('supply_id', '=', $postData['supply_id'])
                        ->get()->first();
        if (!$supply) {
            $supply = new SellOffersSupplies();
            $supply_exist = false;
        }
       
        $supply->supply_id = $postData['supply_id'];
        $supply->selloffer_id = $postData['selloffer_id'];
        $supply->cantity = $postData['cantity'];
        $supply->pvp = $postData['pvp'];        
        if ($supply_exist === true) {
            $supply->update();
            $responseMessage = "Componente Actualizado";
        } else {
            $supply->save();
            $responseMessage = "Componente Añadido";
        }
        return $responseMessage;
    }
    
    public function deleteSellOfferSupplyAjax($postData){
    
        $supply = SellOffersSupplies::where('id', '=', $postData['id'])
                ->get()->first();
        $supply->delete();
        $responseMessage = "Componente Eliminado";
        return $responseMessage;
    }
    
    public function saveSellOfferWorkAjax($postData){
//        var_dump($postData);  
        $work_exist = true;        
        $work = SellOffersWorks::where('selloffer_id', '=', $postData['selloffer_id'])
                        ->where('work_id', '=', $postData['work_id'])
                        ->get()->first();
        if (!$work) {
            $work = new SellOffersWorks();
            $work_exist = false;
        }
       
        $work->work_id = $postData['work_id'];
        $work->selloffer_id = $postData['selloffer_id'];
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
    
    public function deleteSellOfferWorkAjax($postData){
    
        $work = SellOffersWorks::where('id', '=', $postData['id'])
                ->get()->first();
        $work->delete();
        $responseMessage = "Componente Eliminado";
        return $responseMessage;
    }
    
    public function getLastOfferNumber(){
        $lastOfferNumber = SellOffer::select('selloffers.offerNumber')
                ->get()->last();
        return $lastOfferNumber;
        
    }
    
}