<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Controllers\Buys;

use App\Controllers\BaseController;
use App\Models\VehicleTypes;
use App\Services\Buys\VehicleTypeService;
use Laminas\Diactoros\ServerRequest;
use Respect\Validation\Validator as v;
use Symfony\Component\Config\Definition\Exception\Exception;
use Laminas\Diactoros\Response\RedirectResponse;

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
    }
    
    
    
    public function getIndexAction()
    {
        $types = VehicleTypes::All();
        return $this->renderHTML('/vehiclesTypes/vehiclesTypesList.html.twig', [
            'currentUser' => $this->currentUser->getCurrentUserEmailAction(),
            'types' => $types
        ]);
    }
    public function searchVehicleTypeAction($request)
    {
        $searchData = $request->getParsedBody();
        $searchString = $searchData['searchFilter'];        
        $type = VehicleTypes::Where("name", "like", "%".$searchString."%")
                ->orWhere("id", "like", "%".$searchString."%")                
                ->get();     
        return $this->renderHTML('/vehiclesTypes/vehiclesTypesList.twig', [
            'currentUser' => $this->currenUser->getCurrentUserEmailAction(),
            'types' => $type
                
        ]);
    }
    
    public function getVehicleTypesDataAction($request)
    {                
        $responseMessage = null;
        if($request->getMethod() == 'POST')
        {
            $postData = $request->getParsedBody();            
            $typeValidator = v::key('name', v::stringType()->notEmpty());           
            try{
                $typeValidator->assert($postData); // true 
                $type = new VehicleTypes();
                $type->name = $postData['name'];                          
                $type->save();     
                $responseMessage = 'Saved';     
            }catch(Exception $e){                
                $responseMessage = $e->getMessage();
            }              
        }
        $typeSelected = null;
        if($_GET)
        {
            $typeSelected = VehicleTypes::find($_GET['id']);
        }
        return $this->renderHTML('/vehiclesTypes/vehiclesTypesForm.html.twig', [
            'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
            'responseMessage' => $responseMessage,
            'type' => $typeSelected
        ]);
    }

    public function deleteAction(ServerRequest $request)
    {         
        $this->vehiclesTypeService->deleteVehicleType($request->getQueryParams('id'));               
        return new RedirectResponse('/intranet/vehicles/vehicleTypes/list');
    }

}
