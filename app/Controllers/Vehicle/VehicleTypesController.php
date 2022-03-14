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
use Laminas\Diactoros\Response\RedirectResponse;
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
    protected $list = '/Intranet/vehicles/vehicleTypes/list';
    protected $tab = 'buys';
    protected $title = 'Tipos de Vehiculo';
    protected $save = "/Intranet/vehicles/vehicleTypes/save";
    protected $formName = "vehicleTypesForm";
    protected $inputs = ['id' => ['id' => 'inputID', 'name' => 'id', 'title' => 'ID'],  
        'name' => ['id' => 'inputName', 'name' => 'name', 'title' => 'Nombre']];
    public function __construct(VehicleTypeService $vehicleTypeService) {
        parent::__construct();
        $this->vehicleTypeService = $vehicleTypeService;
    }   
    public function getIndexAction()  {
        $types = $this->vehicleTypeService->getAllRegisters(new VehicleTypes());
        return $this->renderHTML('/vehicles/vehiclesTypes/vehiclesTypesList.html.twig', [
            'currentUser' => $this->currentUser->getCurrentUserEmailAction(),
            'list' => $this->list,
            'tab' => $this->tab,
            'title' => $this->title,
            'types' => $types
        ]);
    }   
    public function getVehicleTypesDataAction($request) {                
        $responseMessage = null;
        if($request->getMethod() == 'POST') {
            $postData = $request->getParsedBody();            
            $typeValidator = v::key('name', v::stringType()->notEmpty());           
            try{
                $typeValidator->assert($postData); // true  
                $responseMessage = $this->vehicleTypeService->saveRegister(new VehicleTypes(), $postData);
            }catch(Exception $e){                
                $responseMessage = $this->errorService->getError($e);
            }           
        }        
        $typeSelected = $this->vehicleTypeService->setInstance(new VehicleTypes(), $request->getQueryParams('id'));                
        return $this->renderHTML('/vehicles/vehiclesTypes/vehiclesTypesForm.html.twig', [
            'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
            'responseMessage' => $responseMessage,
            'inputs' => $this->inputs,
            'save' => $this->save,
            'list' => $this->list,
            'formName' => $this->formName,
            'tab' => $this->tab,
            'title' => $this->title,
            'value' => $typeSelected
        ]);
    }   
    public function deleteAction(ServerRequest $request)  {         
        $this->vehicleTypeService->deleteRegister(new VehicleTypes(), $request->getQueryParams('id'));              
        return new RedirectResponse($this->list);
    }
}
