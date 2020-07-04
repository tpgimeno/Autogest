<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Controllers\Buys;

use App\Controllers\BaseController;
use App\Models\Brand;
use App\Models\ModelVh;
use App\Models\Vehicle;
use App\Models\VehiclesType;
use App\Services\Buys\VehicleService;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Diactoros\ServerRequest;
use Respect\Validation\Validator as v;
use Symfony\Component\Config\Definition\Exception\Exception;

/**
 * Description of VehiclesController
 *
 * @author tonyl
 */
class VehicleController extends BaseController
{
    protected $vehicleService;   
    
    public function __construct(VehicleService $vehicleService) {
        parent::__construct();
        $this->vehicleService = $vehicleService;
    }
    
    public function getIndexAction()
    {
        $vehicles = Vehicle::All();
        $marcas = Brand::All();
        $modelos = ModelVh::All();
        $types = VehiclesType::All();
        return $this->renderHTML('/vehicles/vehiclesList.html.twig', [
            'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
            'vehicles' => $vehicles,
            'marcas' => $marcas,
            'modelos' => $modelos,
            'types' => $types
        ]);
    }
    
    public function searchVehicleAction($searchString)
    {
        $vehicles = Vehicle::Where("brand", "like", "%".$searchString."%" )
                ->orWhere("model", "like", "%".$searchString."%")
                ->orWhere("description", "like", "%".$searchString."%")
                ->orWhere("plate", "like", "%".$searchString."%")
                ->orWhere("vin", "like", "%".$searchString."%")
                ->orWhere("type", "like", "%".$searchString."%")
                ->orWhere("id", "like", "%".$searchString."%")
                ->get();
        
        $marcas = Brand::All();
        $modelos = ModelVh::All();
        $types = VehiclesType::All();
        return $this->renderHTML('/vehicles/vehiclesList.html.twig',[
            'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
            'vehicles' => $vehicles,
            'marcas' => $marcas,
            'modelos' => $modelos,
            'types' => $types
        ]);
    }    
    public function getVehicleDataAction($request)
    {                
        $responseMessage = null;        
        if($request->getMethod() == 'POST')
        {
            $postData = $request->getParsedBody();            
            $vehicleValidator = v::key('plate', v::stringType()->notEmpty()) 
            ->key('vin', v::stringType()->notEmpty())
            ->key('brand', v::stringType()->notEmpty())
            ->key('model', v::stringType()->notEmpty());            
            try{
                $vehicleValidator->assert($postData); // true 
                $vehicle = new Vehicle();
                $vehicle->id = $postData['id'];
                if($vehicle->id)
                {
                    $vh_temp = Vehicle::find($vehicle->id)->first();
                    if($vh_temp)
                    {
                        $vehicle = $vh_temp;
                    }
                }
                $vehicle->brand = $postData['brand'];
                $vehicle->model = $postData['model'];               
                $vehicle->description = $postData['description'];
                $vehicle->plate = $postData['plate'];
                $vehicle->vin = $postData['vin'];
                $vehicle->type = $postData['type'];
                $vehicle->location = $postData['location'];                
                $vehicle->power = $postData['power'];
                $vehicle->places = $postData['places'];
                $vehicle->color = $postData['color'];
                $vehicle->km = $postData['km'];
                $vehicle->cost = $postData['cost'];
                $vehicle->pvp = $postData['pvp'];                
                $accesories = array_filter($postData , function($string){
                    $findString = 'acc-';
                    if(stripos($string, $findString) === 0)
                    {
                        return $string;
                    }
                });    
                $accesories = array_unique($accesories);
                $vehicle->accesories = implode(", ", $accesories);
                $vehicle->doors = $postData['doors'];                         
                if($vh_temp)
                {
                    $vehicle->update();
                    $responseMessage = 'Updated';
                }
                else
                {
                    $vehicle->save();     
                    $responseMessage = 'Saved';  
                }
                   
            }catch(Exception $e){                
                $responseMessage = $e->getMessage();
            }              
        }
        $vehicleSelected = null;   
        $accesories_withkeys = null;
        if($request->getQueryParams())
        {
            $vehicleSelected = Vehicle::find($request->getQueryParams('id'))->first();
            $accesories = explode(", ", $vehicleSelected->accesories);            
            foreach($accesories as $key => $acc)
            {            
               $key = $acc;  
               $accesories_withkeys[$key] = $acc;
            }      
        }
        $brands = Brand::All();
        $models = ModelVh::All();
        $types = VehiclesType::All();                 
        return $this->renderHTML('/vehicles/vehiclesForm.html.twig', [
            'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
            'responseMessage' => $responseMessage,
            'brands' => $brands,
            'models' => $models,
            'types' => $types,
            'vehicle' => $vehicleSelected,
            'accesories' => $accesories_withkeys
        ]);
    }
    public function deleteAction(ServerRequest $request)
    {         
        $this->vehicleService->deleteVehicle($request->getQueryParams('id'));               
        return new RedirectResponse('/intranet/vehicles/list');
    }
    
}
