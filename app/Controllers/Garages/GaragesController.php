<?php

namespace App\Controllers\Garages;

use App\Controllers\BaseController;
use App\Models\Garage;
use App\Services\Buys\GarageService;
use Exception;
use Laminas\Diactoros\ServerRequest;
use Respect\Validation\Validator as v;

class GaragesController extends BaseController {     
    protected $garageService;
    
    public function __construct(GarageService $garageService) {
        parent::__construct();
        $this->garageService = $garageService;
        $this->model = new Garage();
        $this->route = 'garages';
        $this->titleList = 'Talleres';
        $this->titleForm = 'Taller';
        $this->labels = $this->garageService->getLabelsArray(); 
        $this->itemsList = array('id', 'name', 'email', 'phone');
        $this->properties = $this->garageService->getModelProperties($this->model);
    }     
    public function getIndexAction($request) {
        return $this->getBaseIndexAction($request, $this->model, null);
    }      
    
    public function getGarageDataAction($request) {                
        $responseMessage = null;
        if($request->getMethod() == 'POST') {
            $postData = $request->getParsedBody();            
            $garageValidator = v::key('name', v::stringType()->notEmpty()) 
            ->key('fiscalId', v::notEmpty())
            ->key('phone', v::notEmpty())
            ->key('email', v::stringType()->notEmpty());            
            try{
                $garageValidator->assert($postData); // true                                  
            }catch(Exception $e){                
                $responseMessage = $this->errorService->getError($e);
            } 
            return $this->getBasePostDataAction($request, $this->model, null, $responseMessage);
        }else{
            return $this->getBaseGetDataAction($request, $this->model, null);
        }        
    }
    public function deleteAction(ServerRequest $request) {         
        return $this->deleteItemAction($request, $this->model);
    }
}