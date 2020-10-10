<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Controllers\Buys;

use App\Controllers\BaseController;
use App\Models\Brand;
use App\Models\Location;
use App\Models\ModelVh;
use App\Models\Vehicle;
use App\Models\VehicleTypes;
use App\Services\Buys\VehicleService;
use Illuminate\Database\Capsule\Manager as DB;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Diactoros\ServerRequest;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use Respect\Validation\Validator as v;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpKernel\HttpCache\Store;

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
        $vehicles = DB::table('vehicles')  
                ->join('brands', 'vehicles.brand', '=', 'brands.id')
                ->join('models', 'vehicles.model', '=', 'models.id')
                ->select('vehicles.id', 'brands.name as brand', 'models.name as model', 'vehicles.description', 'vehicles.plate', 'vehicles.vin')
                ->orderBy('brand', 'asc')
                ->orderBy('model', 'asc')
                ->orderBy('vehicles.plate', 'asc')                
                ->whereNull('vehicles.deleted_at')                
                ->get();       
        return $this->renderHTML('/vehicles/vehiclesList.html.twig', [
            'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
            'vehicles' => $vehicles
        ]);
    }
    
    public function searchVehicleAction($request)
    {
        $postData = $request->getParsedBody();
        $searchString = $postData['searchFilter'];
        $vehicles = DB::table('vehicles')
                ->join('brands', 'vehicles.brand', '=', 'brands.id')
                ->join('models', 'vehicles.model', '=', 'models.id')
                ->join('vehicle_types', 'vehicles.type', '=', 'vehicle_types.id')
                ->select('vehicles.id as id', 'vehicles.plate as plate', 'vehicles.vin as vin', 
                        'vehicles.description as description', 'vehicles.location', 'vehicle_types.name as type', 
                        'brands.name as brand', 'models.name as model', 'vehicles.color', 'vehicles.places',
                        'vehicles.doors', 'vehicles.power', 'vehicles.cost', 'vehicles.pvp', 'vehicles.accesories')
                ->orderBy('brand', 'asc')
                ->orderBy('model', 'asc')
                ->orderBy('vehicles.plate', 'asc')  
                ->where("brands.name", "like", "%".$searchString."%" )
                ->orWhere("models.name", "like", "%".$searchString."%")
                ->orWhere("vehicles.description", "like", "%".$searchString."%")
                ->orWhere("vehicles.plate", "like", "%".$searchString."%")
                ->orWhere("vehicles.vin", "like", "%".$searchString."%")
                ->orWhere("vehicle_types.name", "like", "%".$searchString."%")
                ->orWhere("vehicles.id", "like", "%".$searchString."%")
                ->WhereNull('vehicles.deleted_at')
                ->get();         
        $marcas = Brand::All();
        $modelos = ModelVh::All();
        $types = VehicleTypes::All();
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
        $vh_temp = null;
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
                    $vh_temp = Vehicle::find($vehicle->id);
                    if($vh_temp)
                    {
                        $vehicle = $vh_temp;
                    }
                } 
                $brand = Brand::where('name', '=', $postData['brand'])->first();
                $vehicle->brand = $brand->id;
                $model = ModelVh::where('name', '=', $postData['model'])->first();
                $vehicle->model = $model->id;               
                $vehicle->description = $postData['description'];
                $vehicle->plate = $postData['plate'];
                $vehicle->vin = $postData['vin'];
                $vehicle->registry_date = date($postData['matriculacion']);
                $type = VehicleTypes::where('name', '=', $postData['type'])->first();
                $vehicle->type = $type->id;
                $store = Store::where('name', '=', $postData['store'])->first();
                $vehicle->store = $store->id;
                $location = Location::where('name', '=', $postData['location'])->first();
                $vehicle->location = $location->id;                
                $vehicle->power = $postData['power'];
                $vehicle->places = $postData['places'];
                $vehicle->color = $postData['color'];
                $vehicle->km = $postData['km'];
                $vehicle->cost = $postData['inputBuy'];
                $vehicle->pvp = $postData['pvp'];                
                $accesories = array_filter($postData , function($string){
                    $findString = 'acc-';
                    if(stripos($string, $findString) === 0)
                    {
                        return $string;
                    }
                });    
                $accesories_ord = \array_unique($accesories);
                $vehicle->accesories = implode(", ", $accesories_ord);
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
                   
            }catch(Exception $e)
            {                
                $responseMessage = $this->errorService->getError($e);
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
        $types = VehicleTypes::All();                 
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
    
    public function importExcel()
    {
        $responseMessage = null;
        $reader = new Xls();
        $reader->setLoadSheetsOnly('GENERAL');
        $reader->setReadDataOnly(true);        
        $spreadSheet = $reader->load('VEHICULOS 01-08-19 UBICACIONES.xls');
        $vehiculos = $spreadSheet->getActiveSheet()->toArray();         
//        var_dump($vehiculos);die();
        try{
            for($i = 1; $i < intval(count($vehiculos)); $i++)
            {
                $vehiculo = new Vehicle(); 
                $vehiculo->brand = $vehiculos[$i][0];
                $vehiculo->model = $vehiculos[$i][1];
                $vehiculo->description = $vehiculos[$i][2];
                $vehiculo->plate = $vehiculos[$i][3];
                $vehiculo->vin = null;
                $vehiculo->registry_date = date('D-M-YY', intval($vehiculos[$i][8]));            
                $vehiculo->store = $vehiculos[$i][4];
                $vehiculo->location = 0;
                $vehiculo->type = $vehiculos[$i][5];
                $vehiculo->color = null;
                $vehiculo->places = 0;
                $vehiculo->doors = 0;
                $vehiculo->power = 0;
                $vehiculo->km = $vehiculos[$i][7];
                $vehiculo->cost = 0;
                $vehiculo->pvp = intval($vehiculos[$i][10])/1.21;
                $vehiculo->save(); 
                $responseMessage = 'Saved';               
            }
        } 
        catch (Exception $ex) 
        {
            $responseMessage = $ex->getMessage();            
        }        
        $vehicles = DB::table('vehicles')  
                ->join('brands', 'vehicles.brand', '=', 'brands.id')
                ->join('models', 'vehicles.model', '=', 'models.id')
                ->select('vehicles.id', 'brands.name as brand', 'models.name as model', 'vehicles.description', 'vehicles.plate', 'vehicles.vin')
                ->whereNull('vehicles.deleted_at')
                ->orderBy('brand', 'asc')
                ->orderBy('model', 'asc')
                ->orderBy('vehicles.plate', 'asc')  
                ->get();       
        return $this->renderHTML('/vehicles/vehiclesList.html.twig', [
            'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
            'vehicles' => $vehicles
        ]);
        
    }
    
    public function deleteAction(ServerRequest $request)
    {         
        $this->vehicleService->deleteVehicle($request->getQueryParams('id'));               
        return new RedirectResponse('/intranet/vehicles/list');
    }
    
}
