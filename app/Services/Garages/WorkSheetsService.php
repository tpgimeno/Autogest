<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace App\Services\Garages;

use App\Models\Vehicle;
use App\Models\WorkSheetComponents;
use App\Models\WorkSheets;
use App\Models\WorkSheetSupplies;
use App\Models\WorkSheetWorks;
use App\Services\BaseService;

/**
 * Description of WorkSheetsService
 *
 * @author tonyl
 */
class WorkSheetsService extends BaseService{
    
    public function list(){
        $values = WorkSheets::join('garage_orders', 'garage_orders.id', '=', 'worksheets.order_id')
                ->join('vehicles', 'garage_orders.vehicle_id', '=', 'vehicles.id')
                ->join('customers', 'garage_orders.customer_id', '=', 'customers.id')
                ->join('works', 'worksheets.workId', '=', 'works.id')
                ->join('brands', 'vehicles.brand_id', '=', 'brands.id')
                ->join('models', 'vehicles.model_id', '=', 'models.id')
                ->get(['worksheets.id as id', 'customers.name as customer', 'brands.name as brand', 'models.name as model', 'works.name as work'])
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
    
    public function getWorkSheetComponents($request){
        $components = null;
        if ($request->getMethod() == 'POST') {
            $postData = $request->getParsedBody();
            if(isset($postData['id'])){
                $components = WorkSheetComponents::join('components', 'worksheetsComponents.component_id', '=', 'components.id')
                        ->join('maders', 'components.mader_id', '=', 'maders.id')
                        ->where('worksheetsComponents.workSheet_id', '=', intval($postData['id']))
                        ->select('components.id as component_id', 'worksheetsComponents.id as workSheetComponent_id', 'maders.name as mader', 'components.ref as ref', 'components.name as name', 'worksheetsComponents.pvp', 'worksheetsComponents.cantity')
                        ->get();
            }
        }else{
            $params = $request->getQueryParams();
            if(isset($params['id'])){
                $components = WorkSheetComponents::join('components', 'worksheetsComponents.component_id', '=', 'components.id')
                        ->join('maders', 'components.mader_id', '=', 'maders.id')
                        ->where('worksheetsComponents.workSheet_id', '=', intval($params['id']))
                        ->select('components.id as component_id', 'worksheetsComponents.id as workSheetComponent_id', 'maders.name as mader', 'components.ref as ref', 'components.name as name', 'worksheetsComponents.pvp', 'worksheetsComponents.cantity')
                        ->get();
            }
        }
        return $components;
    }
    
    public function addComponentsWorkSheetsAction($postData){ 
//        var_dump($postData);die();
        $component_exist = true;        
        $component = WorksheetComponents::where('workSheet_id', '=', $postData['workSheet_id'])                
                        ->where('component_id', '=', $postData['component_id'])
                        ->get()->first();
        if (!$component) {
            $component = new WorkSheetComponents();
            $component_exist = false;
        }
       
        $component->component_id = $postData['component_id'];
        $component->workSheet_id = $postData['workSheet_id'];
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
    
    public function delComponentsWorkSheetsAction($postData){
        $component = WorksheetComponents::where('id', '=', $postData['id'])
                ->get()->first();
        $component->delete();
        $responseMessage = "Componente Eliminado";
        return $responseMessage;
    }
    
    public function getWorkSheetSupplies($request){
        $supplies = null;
        if ($request->getMethod() == 'POST') {
            $postData = $request->getParsedBody();
            if(isset($postData['id'])){
                $supplies = WorkSheetSupplies::join('supplies', 'worksheetssupplies.supply_id', '=', 'supplies.id')
                        ->join('maders', 'supplies.mader_id', '=', 'maders.id')
                        ->where('worksheetssupplies.workSheet_id', '=', intval($postData['id']))
                        ->select('supplies.id as supply_id', 'worksheetssupplies.id as workSheetSupply_id', 'maders.name as mader', 'supplies.ref as ref', 'supplies.name as name', 'worksheetssupplies.pvp', 'worksheetssupplies.cantity')
                        ->get();
            }
        }else{
            $params = $request->getQueryParams();
            if(isset($params['id'])){
                $supplies = WorkSheetSupplies::join('supplies', 'worksheetssupplies.supply_id', '=', 'supplies.id')
                        ->join('maders', 'supplies.mader_id', '=', 'maders.id')
                        ->where('worksheetssupplies.workSheet_id', '=', intval($params['id']))
                        ->select('supplies.id as supply_id', 'worksheetssupplies.id as workSheetSupply_id', 'maders.name as mader', 'supplies.ref as ref', 'supplies.name as name', 'worksheetssupplies.pvp', 'worksheetssupplies.cantity')
                        ->get();
            }
        }
        return $supplies;
    }
    
    public function addSuppliesWorkSheetsAction($postData){
//        var_dump($postData);  
        $supply_exist = true;        
        $supply = WorkSheetSupplies::where('workSheet_id', '=', $postData['workSheet_id'])
                        ->where('supply_id', '=', $postData['supply_id'])
                        ->get()->first();
        if (!$supply) {
            $supply = new WorkSheetSupplies();
            $supply_exist = false;
        }
       
        $supply->supply_id = $postData['supply_id'];
        $supply->workSheet_id = $postData['workSheet_id'];
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
    
    public function delSuppliesWorkSheetsAction($postData){
        
        $supply = WorkSheetSupplies::where('id', '=', $postData['id'])
                ->get()->first();
        $supply->delete();
        $responseMessage = "Recambio Eliminado";
        return $responseMessage;
    }
    
    public function getWorkSheetWorks($request){
        $works = null;
        if ($request->getMethod() == 'POST') {
            $postData = $request->getParsedBody();
            if(isset($postData['id'])){
                $works = WorkSheetWorks::join('works', 'worksheetsworks.work_id', '=', 'works.id')                        
                        ->where('worksheetsworks.workSheet_id', '=', intval($postData['id']))
                        ->select('works.id as work_id', 'worksheetsworks.id as workSheetWork_id', 'works.ref as ref', 'works.name as name', 'worksheetsworks.pvp', 'worksheetsworks.cantity')
                        ->get();
            }
        }else{
            $params = $request->getQueryParams();
            if(isset($params['id'])){
                $works = WorkSheetWorks::join('works', 'worksheetsworks.work_id', '=', 'works.id')                       
                        ->where('worksheetsworks.workSheet_id', '=', intval($params['id']))
                        ->select('works.id as work_id', 'worksheetsworks.id as workSheetWork_id', 'works.ref as ref', 'works.name as name', 'worksheetsworks.pvp', 'worksheetsworks.cantity')
                        ->get();
            }
        }
        return $works;
    }
    
    public function addWorksWorkSheetsAction($postData){
//        var_dump($postData);  
        $work_exist = true;        
        $work = WorkSheetWorks::where('workSheet_id', '=', $postData['workSheet_id'])
                        ->where('work_id', '=', $postData['work_id'])
                        ->get()->first();
        if (!$work) {
            $work = new WorkSheetWorks();
            $work_exist = false;
        }
       
        $work->work_id = $postData['work_id'];
        $work->workSheet_id = $postData['workSheet_id'];
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
    
    public function delWorksWorkSheetsAction($postData){    
        $work = WorkSheetWorks::where('id', '=', $postData['id'])
                ->get()->first();
        $work->delete();
        $responseMessage = "Trabajo Eliminado";
        return $responseMessage;
    }
    
    public function getLastWorkSheetNumber(){
        $lastWorkSheetNumber = WorkSheets::select('worksheets.workSheetNumber')
                ->get()->last();
        return $lastWorkSheetNumber;        
    }
}
