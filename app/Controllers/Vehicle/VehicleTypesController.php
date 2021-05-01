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
use Illuminate\Database\QueryException;
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
    
    public function __construct(VehicleTypeService $vehicleTypeService) {
        parent::__construct();
        $this->vehicleTypeService = $vehicleTypeService;
    }   
    public function getIndexAction()
    {
        $types = VehicleTypes::All();
        return $this->renderHTML('/vehicles/vehiclesTypes/vehiclesTypesList.html.twig', [
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
        return $this->renderHTML('/vehicles/vehiclesTypes/vehiclesTypesList.twig', [
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
            }catch(Exception $e){                
                $responseMessage = $this->errorService->getError($e);
            }
            $type = new VehicleTypes();
            if(isset($postData['id']) && $postData['id'])
            {
                $type = VehicleTypes::find($postData['id'])->first();
            }
            $type->name = $postData['name'];                
            $responseMessage = $this->saveVehicleType($type); 
        }
        $typeSelected = null;
        if($request->getQueryParams('id'))
        {
            $typeSelected = VehicleTypes::find($request->getQueryParams('id'))->first();
        }        
        return $this->renderHTML('/vehicles/vehiclesTypes/vehiclesTypesForm.html.twig', [
            'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
            'responseMessage' => $responseMessage,
            'type' => $typeSelected
        ]);
    }
    public function saveVehicleType($type){
        try{
            if(VehicleTypes::find($type->id))
            {
                $type->update();
                $message = 'Updated';
            }
            else
            {
                $type->save();
                $message = 'Saved';
            }
        } catch (QueryException $ex){
            $message = $this->errorService->getError($ex);
        }        
        return $message;
    }
    public function deleteAction(ServerRequest $request)
    {         
        $this->vehicleTypeService->deleteVehicleType($request->getQueryParams('id'));               
        return new RedirectResponse('/intranet/vehicles/vehicleTypes/list');
    }

}
