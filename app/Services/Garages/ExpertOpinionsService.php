<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace App\Services\Garages;

use App\Models\ExpertOpinionComponents;
use App\Models\ExpertOpinions;
use App\Models\ExpertOpinionSupplies;
use App\Models\ExpertOpinionWorks;
use App\Models\Vehicle;
use App\Services\BaseService;

/**
 * Description of ExpertOpinionsService
 *
 * @author tonyl
 */
class ExpertOpinionsService extends BaseService{
    
    public function list(){
        $values = ExpertOpinions::join('vehicles', 'expertopinions.vehicle_id', '=', 'vehicles.id')                
                ->join('brands', 'vehicles.brand_id', '=', 'brands.id')
                ->join('models', 'vehicles.model_id', '=', 'models.id')
                ->get(['expertopinions.id as id', 'expertopinions.expertName as name', 'expertopinions.expertSurname as surname', 'vehicles.plate as plate', 'expertopinions.date as date'])
                ->toArray();
        return $values;
    }
    
    public function getOpinionVehicles(){
        $vehicles = Vehicle::join('brands', 'vehicles.brand_id', '=', 'brands.id')
                ->join('models', 'vehicles.model_id', '=', 'models.id')
                ->select('vehicles.id', 'vehicles.plate', 'vehicles.vin', 'vehicles.km' , 'vehicles.brand_id', 'brands.name as brand', 'vehicles.model_id', 'models.name as model', 'vehicles.vehiclePvp')
                ->get()->toArray();
        return $vehicles;
    }
    
    public function getVehiclesByPlate($data){
        $vehicles = Vehicle::join('brands', 'vehicles.brand_id', '=', 'brands.id')
                ->join('models', 'vehicles.model_id', '=', 'models.id')
                ->select('vehicles.id', 'vehicles.plate', 'vehicles.vin', 'vehicles.km' , 'vehicles.brand_id', 'brands.name as brand', 'vehicles.model_id', 'models.name as model', 'vehicles.vehiclePvp')
                ->where('vehicles.plate', '=', $data['plate'])
                ->get()->toArray();
        return $vehicles;
    }
    
    public function getLastOpinionId(){
        $lastOpinionId = ExpertOpinions::select('expertopinions.opinionId')
                ->get()->last();
        return $lastOpinionId;        
    }
    
    public function getExpertOpinionsComponents($request){
        $components = null;
        if ($request->getMethod() == 'POST') {
            $postData = $request->getParsedBody();
            if(isset($postData['id'])){
                $components = ExpertOpinionComponents::join('components', 'expertopinionscomponents.component_id', '=', 'components.id')
                        ->join('maders', 'components.mader_id', '=', 'maders.id')
                        ->where('expertopinionscomponents.expertOpinion_id', '=', intval($postData['id']))
                        ->select('components.id as component_id', 'expertopinionscomponents.id as expertOpinionComponent_id', 'maders.name as mader', 'components.ref as ref', 'components.name as name', 'expertopinionscomponents.pvp', 'expertopinionscomponents.cantity')
                        ->get();
            }
        }else{
            $params = $request->getQueryParams();
            if(isset($params['id'])){
                $components = ExpertOpinionComponents::join('components', 'expertopinionscomponents.component_id', '=', 'components.id')
                        ->join('maders', 'components.mader_id', '=', 'maders.id')
                        ->where('expertopinionscomponents.expertOpinion_id', '=', intval($params['id']))
                        ->select('components.id as component_id', 'expertopinionscomponents.id as expertOpinionComponent_id', 'maders.name as mader', 'components.ref as ref', 'components.name as name', 'expertopinionscomponents.pvp', 'expertopinionscomponents.cantity')
                        ->get();
            }
        }
//        var_dump($params);die();
        return $components;
    }
    
    public function addComponentsExpertOpinionsAction($postData){ 
       
        $component_exist = true;        
        $component = ExpertOpinionComponents::where('expertOpinion_id', '=', $postData['expertOpinion_id'])                
                        ->where('component_id', '=', $postData['component_id'])
                        ->get()->first();
        if (!$component) {
            $component = new ExpertOpinionComponents();
            $component_exist = false;
        }
       
        $component->component_id = $postData['component_id'];
        $component->expertOpinion_id = $postData['expertOpinion_id'];
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
    
    public function delComponentsExpertOpinionsAction($postData){
        $component = ExpertOpinionComponents::where('id', '=', $postData['id'])
                ->get()->first();
        $component->delete();
        $responseMessage = "Componente Eliminado";
        return $responseMessage;
    }
    
    public function getExpertOpinionsSupplies($request){
        $supplies = null;
        if ($request->getMethod() == 'POST') {
            $postData = $request->getParsedBody();
            if(isset($postData['id'])){
                $supplies = ExpertOpinionSupplies::join('supplies', 'expertopinionssupplies.supply_id', '=', 'supplies.id')
                        ->join('maders', 'supplies.mader_id', '=', 'maders.id')
                        ->where('expertopinionssupplies.expertOpinion_id', '=', intval($postData['id']))
                        ->select('supplies.id as supply_id', 'expertopinionssupplies.id as expertOpinionSupply_id', 'maders.name as mader', 'supplies.ref as ref', 'supplies.name as name', 'expertopinionssupplies.pvp', 'expertopinionssupplies.cantity')
                        ->get();
            }
        }else{
            $params = $request->getQueryParams();
            if(isset($params['id'])){
                $supplies = ExpertOpinionSupplies::join('supplies', 'expertopinionssupplies.supply_id', '=', 'supplies.id')
                        ->join('maders', 'supplies.mader_id', '=', 'maders.id')
                        ->where('expertopinionssupplies.expertOpinion_id', '=', intval($params['id']))
                        ->select('supplies.id as supply_id', 'expertopinionssupplies.id as expertOpinionSupply_id', 'maders.name as mader', 'supplies.ref as ref', 'supplies.name as name', 'expertopinionssupplies.pvp', 'expertopinionssupplies.cantity')
                        ->get();
            }
        }
        return $supplies;
    }
    
    public function addSuppliesExpertOpinionsAction($postData){
//        var_dump($postData);  
        $supply_exist = true;        
        $supply = ExpertOpinionSupplies::where('expertopinion_id', '=', $postData['expertOpinion_id'])
                        ->where('supply_id', '=', $postData['supply_id'])
                        ->get()->first();
        if (!$supply) {
            $supply = new ExpertOpinionSupplies();
            $supply_exist = false;
        }
       
        $supply->supply_id = $postData['supply_id'];
        $supply->expertOpinion_id = $postData['expertOpinion_id'];
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
    
    public function delSuppliesExpertOpinionsAction($postData){    
        $supply = ExpertOpinionSupplies::where('id', '=', $postData['id'])
                ->get()->first();
        $supply->delete();
        $responseMessage = "Recambio Eliminado";
        return $responseMessage;
    }
    
    public function getExpertOpinionsWorks($request){
        $works = null;
        if ($request->getMethod() == 'POST') {
            $postData = $request->getParsedBody();
            if(isset($postData['id'])){
                $works = ExpertOpinionWorks::join('works', 'expertopinionsworks.work_id', '=', 'works.id')                        
                        ->where('expertopinionsworks.expertOpinion_id', '=', intval($postData['id']))
                        ->select('works.id as work_id', 'expertopinionsworks.id as expertOpinionWork_id', 'works.ref as ref', 'works.name as name', 'expertopinionsworks.pvp', 'expertopinionsworks.cantity')
                        ->get();
            }
        }else{
            $params = $request->getQueryParams();
            if(isset($params['id'])){
                $works = ExpertOpinionWorks::join('works', 'expertopinionsworks.work_id', '=', 'works.id')                       
                        ->where('expertopinionsworks.expertOpinion_id', '=', intval($params['id']))
                        ->select('works.id as work_id', 'expertopinionsworks.id as expertOpinionWork_id', 'works.ref as ref', 'works.name as name', 'expertopinionsworks.pvp', 'expertopinionsworks.cantity')
                        ->get();
            }
        }
        return $works;
    }
    
    public function addWorksExpertOpinionsAction($postData){
//        var_dump($postData);  
        $work_exist = true;        
        $work = ExpertOpinionWorks::where('expertOpinion_id', '=', $postData['expertOpinion_id'])
                        ->where('work_id', '=', $postData['work_id'])
                        ->get()->first();
        if (!$work) {
            $work = new ExpertOpinionWorks();
            $work_exist = false;
        }
       
        $work->work_id = $postData['work_id'];
        $work->expertOpinion_id = $postData['expertOpinion_id'];
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
    
    public function delWorksExpertOpinionsAction($postData){    
        $work = ExpertOpinionWorks::where('id', '=', $postData['id'])
                ->get()->first();
        $work->delete();
        $responseMessage = "Trabajo Eliminado";
        return $responseMessage;
    }
}
