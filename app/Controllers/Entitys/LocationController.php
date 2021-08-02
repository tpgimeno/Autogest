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
use App\Services\LocationService;
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
class LocationController extends BaseController
{
    protected $locationService;
    public function __construct(LocationService $locationService) {
        parent::__construct();
        $this->locationService = $locationService;
    }
    public function getIndexAction()
    {
        $locations = DB::table('locations')
                ->join('stores', 'stores.id', '=', 'locations.store_id')                
                ->select('locations.id', 'stores.name as store', 'locations.name')
                ->whereNull('locations.deleted_at')
                ->get();    
        $stores = Store::All();
        return $this->renderHTML('/stores/locationList.html.twig', [
            'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
            'locations' => $locations,
            'stores' => $stores
        ]);
    }
    public function searchLocationAction($request)
    {
        $searchData = $request->getParsedBody();        
        $searchString = $searchData['searchFilter']; 
        if($searchString)
        {
            $locations = DB::table('locations')
                ->join('stores', 'stores.id', '=', 'locations.store_id')                
                ->select('locations.id', 'stores.name as store', 'locations.name')                
                ->where('locations.name', 'like', "%".$searchString."%")
                ->orWhere('stores.name', 'like', "%".$searchString."%") 
                ->whereNull('locations.deleted_at')
                ->get();
        }
        else
        {
             $locations = DB::table('locations')
                ->join('stores', 'stores.id', '=', 'locations.store_id')                
                ->select('locations.id', 'stores.name as store', 'locations.name')
                ->whereNull('locations.deleted_at')
                ->get();
        }
               
        $stores = Store::All();       
        return $this->renderHTML('/stores/locationList.html.twig', [
            'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
            'locations' => $locations,
            'stores' => $stores                
        ]);
    }
    
    public function getLocationDataAction($request)
    {                
        $responseMessage = null;
        if($request->getMethod() == 'POST')
        {
            $postData = $request->getParsedBody();            
            $locationValidator = v::key('name', v::stringType()->notEmpty());        
            try{
                $locationValidator->assert($postData); // true 
                $location = new Location();
                $location->name = $postData['name'];
                $store_id = Store::Where("name", "=", $postData['store'])->first()['id'];                
                $location->store_id = $store_id; 
                $location->save();     
                $responseMessage = 'Saved';     
            }catch(Exception $e){                
                $responseMessage = $e->getMessage();
            }              
        }
        $locationSelected = null;
        if($request->getQueryParams('id'))
        {
            $locationSelected = Location::find($request->getQueryParams('id'))->first();
        }
        $stores = Store::All();
        return $this->renderHTML('/stores/locationForm.html.twig', [
            'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
            'responseMessage' => $responseMessage,
            'location' => $locationSelected,
            'stores' => $stores
        ]);
    }

    public function deleteAction(ServerRequest $request)
    {       
        
        $this->locationService->deleteLocation($request->getQueryParams('id'));               
        return new RedirectResponse('/Intranet/locations/list');
    }

}

