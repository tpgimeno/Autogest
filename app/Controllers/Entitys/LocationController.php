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
use Illuminate\Database\Capsule\Manager as DB;
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
    }
    public function getIndexAction() {
        $locations = $this->locationService->getLocations();  
        $stores = $this->locationService->getStoresNames();
        return $this->renderHTML('/Entitys/stores/locationList.html.twig', [
            'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
            'locations' => $locations,
            'stores' => $stores
        ]);
    }
    public function searchLocationAction($request) {
        $searchData = $request->getParsedBody();        
        $searchString = $searchData['searchFilter']; 
        $locations = $this->locationService->searchLocations($searchString);               
        $stores = $this->locationService->getStoresNames();      
        return $this->renderHTML('/Entitys/stores/locationList.html.twig', [
            'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
            'locations' => $locations,
            'stores' => $stores                
        ]);
    }    
    public function getLocationDataAction($request) {                
        $responseMessage = null;
        if($request->getMethod() == 'POST') {
            $postData = $request->getParsedBody();            
            $locationValidator = v::key('name', v::stringType()->notEmpty());        
            try{                
                $locationValidator->assert($postData); // true                 
                $postData['store'] = $this->locationService->getStoreByName($postData);
                $responseMessage = $this->locationService->saveRegister(new Location(), $postData);
            }catch(Exception $e){                
                $responseMessage = $e->getMessage();
            }              
        }        
        $locationSelected = $this->locationService->setLocationData($request->getQueryParams());   
        $stores = $this->locationService->getStoresNames();
        return $this->renderHTML('/Entitys/stores/locationForm.html.twig', [
            'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
            'responseMessage' => $responseMessage,
            'location' => $locationSelected,
            'stores' => $stores
        ]);
    }
    public function deleteAction(ServerRequest $request) {       
        $this->locationService->deleteRegister(new Location(), $request->getQueryParams('id'));              
        return new RedirectResponse('/Intranet/locations/list');
    }
}

