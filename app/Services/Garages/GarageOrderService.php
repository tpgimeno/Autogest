<?php

namespace App\Services\Garages;

use App\Models\GarageOrderComponent;
use App\Models\Vehicle;
use App\Services\BaseService;


class GarageOrderService extends BaseService
{
    public function getOrderVehicles(){
        $vehicles = Vehicle::join('brands', 'vehicles.brand_id', '=', 'brands.id')
                ->join('models', 'vehicles.model_id', '=', 'models.id')
                ->select('vehicles.id', 'vehicles.plate as plate', 'vehicles.km as km', 'vehicles.brand_id', 'brands.name as brand', 'vehicles.model_id', 'models.name as model')
                ->get()->toArray();
        return $vehicles;
    }
    
    public function getGarageOrderComponents($request){
        $components = null;
        if ($request->getMethod() == 'POST') {
            $postData = $request->getParsedBody();
            if(isset($postData['id'])){
                $components = GarageOrderComponent::join('components', 'garageOrderscomponents.component_id', '=', 'components.id')
                        ->join('maders', 'components.mader_id', '=', 'maders.id')
                        ->where('garageOrderscomponents.garageOrder_id', '=', intval($postData['id']))
                        ->select('components.id as component_id', 'garageOrderscomponents.id as garageOrdercomponent_id', 'maders.name as mader', 'components.ref as ref', 'components.name as name', 'garageOrderscomponents.pvp', 'garageOrderscomponents.cantity')
                        ->get();
            }
        }else{
            $params = $request->getQueryParams();
            if(isset($params['id'])){
                $components = GarageOrderComponent::join('components', 'garageOrderscomponents.component_id', '=', 'components.id')
                        ->join('maders', 'components.mader_id', '=', 'maders.id')
                        ->where('garageOrderscomponents.garageOrder_id', '=', intval($params['id']))
                        ->select('components.id as component_id', 'garageOrderscomponents.id as garageOrdercomponent_id', 'maders.name as mader', 'components.ref as ref', 'components.name as name', 'garageOrderscomponents.pvp', 'garageOrderscomponents.cantity')
                        ->get();
            }
        }
        return $components;
    }
    
}