<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Controllers\Vehicle;

use App\Controllers\BaseController;
use App\Models\VehicleTypes;
use App\Services\Vehicle\VehicleTypeService;
use Laminas\Diactoros\ServerRequest;
use Respect\Validation\Validator as v;
use Symfony\Component\Config\Definition\Exception\Exception;

/**
 * Description of VehicleTypeController
 *
 * @author tonyl
 */
class VehicleTypesController extends BaseController
{
    protected $vehicleTypeService;
    
    public function __construct(VehicleTypeService $vehicleTypeService) {
        parent::__construct();
        $this->vehicleTypeService = $vehicleTypeService;
        $this->model = new VehicleTypes();
        $this->route = 'vehicles/vehicleTypes';
        $this->titleList = 'Tipos de Vehículos';
        $this->titleForm = 'Tipo de Vehículo';
        $this->labels = $this->vehicleTypeService->getLabelsArray(); 
        $this->itemsList = array('id', 'name');
        $this->properties = $this->vehicleTypeService->getModelProperties($this->model);
    }   
    public function getIndexAction($request)  {
        return $this->getBaseIndexAction($request, $this->model, null);
    }   
    public function getVehicleTypesDataAction($request) {                
        $responseMessage = null;
        if($request->getMethod() == 'POST') {
            $postData = $request->getParsedBody();            
            $typeValidator = v::key('name', v::stringType()->notEmpty());           
            try{
                $typeValidator->assert($postData); // true
            }catch(Exception $e){                
                $responseMessage = $this->errorService->getError($e);
            }   
            return $this->getBasePostDataAction($request, $this->model, null, $responseMessage);
        }else{
            return $this->getBaseGetDataAction($request, $this->model, null);
        }       
    }   
    public function deleteAction(ServerRequest $request)  {         
        return $this->deleteItemAction($request, $this->model);
    }
}
