<?php

namespace App\Services\Sales;

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
                        ->where('sellofferscomponents.vehicle_id', '=', intval($postData['id']))
                        ->select('sellofferscomponents.id', 'components.ref as ref', 'components.name as name', 'sellofferscomponents.pvp')
                        ->get();
            }
        }else{
            $params = $request->getQueryParams();
            if(isset($params['id'])){
                $components = SellOffersComponents::join('components', 'sellofferscomponents.component_id', '=', 'components.id')
                        ->where('sellofferscomponents.vehicle_id', '=', intval($params['id']))
                        ->select('sellofferscomponents.id', 'components.ref as ref', 'components.name as name', 'sellofferscomponents.pvp')
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
                        ->where('sellofferssupplies.vehicle_id', '=', $postData['id'])
                        ->select('sellofferssupplies.id', 'supplies.ref as ref', 'supplies.name as name', 'sellofferssupplies.pvp')
                        ->get();
            }
        }else{
            $params = $request->getQueryParams();
            if(isset($params['id'])){
                $supplies = SellOffersSupplies::join('supplies', 'sellofferssupplies.supply_id', '=', 'supplies.id')
                        ->where('sellofferssupplies.vehicle_id', '=', $params['id'])
                        ->select('sellofferssupplies.id', 'supplies.ref as ref', 'supplies.name as name', 'sellofferssupplies.pvp')
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
                        ->where('selloffersworks.vehicle_id', '=', $postData['id'])
                        ->select('selloffersworks.id', 'works.ref as ref', 'works.name as name', 'selloffersworks.pvp')
                        ->get();
            }
        }else{
            $params = $request->getQueryParams();
            if(isset($params['id'])){
                $works = SellOffersWorks::join('works', 'selloffersworks.work_id', '=', 'works.id')
                        ->where('selloffersworks.vehicle_id', '=', $params['id'])
                        ->select('selloffersworks.id', 'works.ref as ref', 'works.name as name', 'selloffersworks.pvp')
                        ->get();
            }
        }
        return $works;
    }
    
    public function getSellOfferVehicles(){       
        $vehicles = Vehicle::join('brands', 'vehicles.brand_id', '=', 'brands.id')
                ->join('models', 'vehicles.model_id', '=', 'models.id')
                ->select('vehicles.id', 'vehicles.plate', 'vehicles.vin', 'vehicles.km' , 'brands.name as brand', 'models.name as model')
                ->get();
        return $vehicles;
    }
    
}