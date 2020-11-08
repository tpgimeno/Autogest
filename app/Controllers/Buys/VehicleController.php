<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Controllers\Buys;

use App\Controllers\BaseController;
use App\Models\Accesories;
use App\Models\Brand;
use App\Models\Location;
use App\Models\ModelVh;
use App\Models\Store;
use App\Models\Vehicle;
use App\Models\VehicleAccesories;
use App\Models\VehicleTypes;
use App\Services\Buys\VehicleService;
use Illuminate\Database\Capsule\Manager as DB;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Diactoros\ServerRequest;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
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
                $brand = Brand::where('name', 'like', "%".$postData['brand']."%")->first();                
                $vehicle->brand = $brand->id;                
                $model = ModelVh::where('name', 'like', "%".$postData['model']."%")->first();                
                $vehicle->model = $model->id;                
                $vehicle->description = $postData['description'];
                $vehicle->plate = $postData['plate'];
                $vehicle->vin = $postData['vin'];
                $vehicle->registry_date = date($postData['registry_date']);                
                $type = VehicleTypes::where('name', 'like', "%".$postData['type']."%")->first();                
                $vehicle->type = $type->id;                
                $store = Store::where('name', 'like', "%".$postData['store']."%")->first();
                if($store)
                {
                    $vehicle->store = $store->id;
                    
                }                
                $location = Location::where('name', 'like', "%".$postData['location']."%")->first();
                if($location)
                {
                    $vehicle->location = $location->id;
                }                                
                $vehicle->power = $postData['power'];
                $vehicle->places = $postData['places'];
                $vehicle->color = $postData['color'];
                $vehicle->km = $postData['km'];
                $vehicle->cost = $postData['cost'];
                $vehicle->pvp = $postData['pvp'];         
                
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
        $store_selected = null;
        $location_selected = null; 
        $selected_accesories = null;
        if($request->getQueryParams())
        {
            $vehicleSelected = Vehicle::find($request->getQueryParams('id'))->first();
            if($vehicleSelected)
            {                          
                $selected_accesories = DB::table('vehicle_accesories')
                        ->join('accesories', 'vehicle_accesories.accesory_id', '=', 'accesories.id')
                        ->select('vehicle_accesories.accesory_id','vehicle_accesories.id', 'accesories.keystring', 'accesories.name')
                        ->where('vehicle_accesories.vehicle_id', '=', $vehicleSelected->id)
                        ->get()->toArray();
//                var_dump($selected_accesories);die();
                if($vehicleSelected->store)
                {
                    $store_selected = Store::find($vehicleSelected->store)->name;
                }  
                if($vehicleSelected->location)
                {
                    $location_selected = Location::find($vehicleSelected->location)->name;
                }
            }                 
        }
        $accesories = Accesories::All();
        $brands = Brand::All();
        $models = ModelVh::All();
        $types = VehicleTypes::All(); 
        $stores = Store::All();
        $locations = Location::All();
        return $this->renderHTML('/vehicles/vehiclesForm.html.twig', [
            'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
            'responseMessage' => $responseMessage,
            'brands' => $brands,
            'models' => $models,
            'stores' => $stores,
            'locations' => $locations,
            'store_selected' => $store_selected,
            'types' => $types,
            'vehicle' => $vehicleSelected,
            'accesories' => $accesories,
            'selected_accesories' => $selected_accesories
        ]);
    }
    public function addAccesoryAction($request)
    {
        $postData = $request->getParsedBody();        
        $getaccesory = json_decode($postData['vhaccesory']);        
        $accesory = Accesories::where('keystring', 'like', "%".$getaccesory->accesory."%")->first();               
        $vehicle_accesory = VehicleAccesories::where('vehicle_id', '=', $getaccesory->vehicle_id)
                ->where('accesory_id', '=', $accesory->id)
                ->first();        
        if($vehicle_accesory === null)
        {
            $vehicle_accesory = new VehicleAccesories();
        }        
        $vehicle_accesory->vehicle_id = $getaccesory->vehicle_id;
        $vehicle_accesory->accesory_id = $accesory->id;
        $vehicle_accesory->save();
        $responseMessage = 'Accesory Saved';
        $response = new JsonResponse($responseMessage);
        return $response;
    }
    public function deleteAccesoryAction($request)
    {
        $responseMessage = null;
        $vehicle_accesory = null;
        $postData = $request->getParsedBody();
        $getaccesory = json_decode($postData['vhaccesory']);        
        $accesory = Accesories::where('keystring', 'like', "%".$getaccesory->accesory."%")->first();               
        $vehicle_accesory = VehicleAccesories::where('vehicle_id', '=', $getaccesory->vehicle_id)
                ->where('accesory_id', '=', $accesory->id)
                ->first();        
        if($vehicle_accesory !== null)
        {
            $responseMessage = 'Accesory Deleted';
            $vehicle_accesory->delete();
        }             
        $response = new JsonResponse($responseMessage);
        return $response;
    }
    public function importExcel()
    {
        setlocale(LC_ALL, 'es_ES');
        $responseMessage = null;
        $reader = new Xls();
        $reader->setLoadSheetsOnly('GENERAL'); 
//        $reader->setReadDataOnly(true);
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
                $time = strtotime($vehiculos[$i][8]);                
                $vehiculo->registry_date = date('Y/m/d', $time);                
                $vehiculo->store = $vehiculos[$i][4];
                $vehiculo->location = 0;
                $vehiculo->type = $vehiculos[$i][5];
                $vehiculo->color = null;
                $vehiculo->places = 0;
                $vehiculo->doors = 0;
                $vehiculo->power = 0;
                $vehiculo->km = $vehiculos[$i][7];
                $vehiculo->cost = 0;
                $vehiculo->pvp = $this->tofloat($vehiculos[$i][10]);                
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
    public function tofloat($num) 
    {
        $dotPos = strrpos($num, '.');
        $commaPos = strrpos($num, ',');
        $sep = (($dotPos > $commaPos) && $dotPos) ? $dotPos :
            ((($commaPos > $dotPos) && $commaPos) ? $commaPos : false);

        if (!$sep) {
            return floatval(preg_replace("/[^0-9]/", "", $num));
        }

        return floatval(
            preg_replace("/[^0-9]/", "", substr($num, 0, $sep)) . '.' .
            preg_replace("/[^0-9]/", "", substr($num, $sep+1, strlen($num)))
        );
    }    
}
