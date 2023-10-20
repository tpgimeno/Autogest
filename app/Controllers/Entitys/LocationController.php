<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Controllers\Entitys;

use App\Controllers\BaseController;
use App\Models\Location;
use App\Models\Store;
use App\Services\Entitys\LocationService;
use Exception;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Diactoros\ServerRequest;
use Respect\Validation\Validator as v;
/**
 * Description of LocationController
 *
 * @author TpGimeno
 */
class LocationController extends BaseController {
    protected $locationService;
    
    public function __construct(LocationService $locationService) {
        parent::__construct();
        $this->locationService = $locationService;
        $this->model = new Location();
        $this->route = 'locations';
        $this->titleList = 'Ubicaciones';
        $this->titleForm = 'Ubicacion';
        $this->labels = $this->locationService->getLabelsArray(); 
        $this->itemsList = array('id', 'store', 'name');
    }
    public function getIndexAction($request) {
        $values = $this->locationService->getLocationItemsList();        
        return $this->getBaseIndexAction($request, $this->model, $values);
    }
     
    public function getLocationDataAction($request) {                
        $responseMessage = null;
        $stores = $this->locationService->getAllRegisters(new Store());
        $iterables = ['store_id' => $stores];
        if($request->getMethod() == 'POST') {
            $postData = $request->getParsedBody();            
            $locationValidator = v::key('name', v::stringType()->notEmpty());        
            try{                
                $locationValidator->assert($postData); // true                 
            }catch(Exception $e){                
                $responseMessage = $e->getMessage();
            }            
            return $this->getBasePostDataAction($request, $this->model, $iterables, $responseMessage);
        }else{
            return $this->getBaseGetDataAction($request, $this->model, $iterables);
        }
    }
    public function deleteAction(ServerRequest $request) {     
        return $this->deleteItemAction($request, new Location());
    }
}

