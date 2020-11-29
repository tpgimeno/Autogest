<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Controllers\Vehicle;

use App\Controllers\BaseController;
use App\Models\Accesories;
use App\Models\Brand;
use App\Models\Location;
use App\Models\ModelVh;
use App\Models\Store;
use App\Models\Vehicle;
use App\Models\VehicleAccesories;
use App\Models\VehicleTypes;
use App\Services\Vehicle\VehicleService;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\QueryException;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Diactoros\ServerRequest;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use Respect\Validation\Validator as v;
use Symfony\Component\Config\Definition\Exception\Exception;
use function GuzzleHttp\json_decode;

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
//        var_dump($vehicles);die();
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
                ->join('vehicletypes', 'vehicles.type', '=', 'vehicletypes.id')
                ->select('vehicles.id as id', 'vehicles.plate as plate', 'vehicles.vin as vin', 
                        'vehicles.description as description', 'vehicles.location', 'vehicletypes.name as type', 
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
                ->orWhere("vehicletypes.name", "like", "%".$searchString."%")
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
        if($request->getMethod() == 'POST')
        {
            $postData = $request->getParsedBody();            
            $vehicleValidator = v::key('plate', v::stringType()->notEmpty());                       
            try{
                $vehicleValidator->assert($postData); // true                  
            }catch(Exception $e)
            {                
                $responseMessage = $e->getMessage();
            }
            
            $vehicle = $this->addVehicleData($postData);            
            $responseMessage = $this->saveVehicle($vehicle);
        }
        $vehicleSelected = null;
        $brandSelected = null;
        $modelSelected = null;
        $storeSelected = null;
        $locationSelected = null;
        $selectedAccesories = null;
        $typeSelected = null;
        if($request->getMethod() == 'GET')
        {
            $vehicleSelected = $this->setVehicle($request);
            
            if($vehicleSelected)
            {
                $brandSelected = $this->setBrand($vehicleSelected);
                $modelSelected = $this->setModel($vehicleSelected);
                $storeSelected = $this->setStore($vehicleSelected);
                $locationSelected = $this->setLocation($vehicleSelected);
                $selectedAccesories = $this->setAccesories($vehicleSelected);
                
                $typeSelected = $this->setVehicleType($vehicleSelected);
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
            'store_selected' => $storeSelected,
            'location_selected' => $locationSelected,
            'types' => $types,
            'type_selected' => $typeSelected,
            'vehicle' => $vehicleSelected,
            'brand_selected' => $brandSelected,
            'model_selected' => $modelSelected,
            'accesories' => $accesories,
            'selected_accesories' => $selectedAccesories
        ]);
    }
    public function saveVehicle($vehicle){
        try{
            if(Vehicle::find($vehicle->id))
            {
                $vehicle->update();
                $responseMessage = 'Updated';
            }
            else
            {
                $vehicle->save();     
                $responseMessage = 'Saved';  
            }
        } catch (Exception $ex) {
            $responseMessage = $this->errorService->getError($ex);
        }
        return $responseMessage;
    }
    public function setVehicle($request){
        $vehicleSelected = null;
        $params = $request->getQueryParams();
        if(isset($params['id']) && $params['id'])
        {
            $vehicleSelected = Vehicle::find($request->getQueryParams('id'))->first();
        }
        return $vehicleSelected;        
    }  
    public function setBrand($vehicleSelected){
        $brandSelected = null;       
        if($vehicleSelected->brand)
        {
            $brandSelected = Brand::find($vehicleSelected->brand)->name;
        }
        return $brandSelected;
    }
    public function setModel($vehicleSelected){
        $modelSelected = null;
        if($vehicleSelected->model)
        {
            $modelSelected = ModelVh::find($vehicleSelected->model)->name;
        }
        return $modelSelected;
    }
    public function setStore($vehicleSelected){
        $store_selected = null;
        if($vehicleSelected->store)
        {
            $store_selected = Store::find($vehicleSelected->store)->name;
        } 
        return $store_selected;        
    }
    public function setLocation($vehicleSelected){
        $location_selected = null; 
        if($vehicleSelected->location)
        {
            $location_selected = Location::find($vehicleSelected->location)->name;
        }
        return $location_selected;
    }
    public function setVehicleType($vehicleSelected){
        $typeSelected = null; 
        if($vehicleSelected->type)
        {            
            $typeSelected = VehicleTypes::find($vehicleSelected->type)->name;            
        }
        return $typeSelected;
    }
    public function setAccesories($vehicleSelected){
        $selected_accesories = null;
        if($vehicleSelected)
        {                          
            $selected_accesories = DB::table('vehicleaccesories')
                    ->join('accesories', 'vehicleaccesories.accesoryId', '=', 'accesories.id')
                    ->select('vehicleaccesories.accesoryId','vehicleaccesories.id', 'accesories.keystring', 'accesories.name')
                    ->where('vehicleaccesories.vehicleId', '=', $vehicleSelected->id)
                    ->get()->toArray();            
        }
        return $selected_accesories;
    }
    public function addVehicleData($postData){
        $vehicle = new Vehicle();                
        if(isset($postData['id']) && $postData['id'])
        {
            $vehicle = Vehicle::find($postData['id']);            
        } 
        $brand = Brand::where('name', 'like', "%".$postData['brand']."%")->first(); 
        if($brand)
        {
            $vehicle->brand = $brand->id;
        }                        
        $model = ModelVh::where('name', 'like', "%".$postData['model']."%")->first();                
        if($model)
        {
            $vehicle->model = $model->id;
        }                        
        $vehicle->description = $postData['description'];
        $vehicle->plate = $postData['plate'];
        if(isset($postData['vin']) && $postData['vin'])
        {
            $vehicle->vin = $postData['vin'];
        }
        else
        {
            $vehicle->vin = null;
        }
        
        $vehicle->registryDate = date($postData['registry_date']);                
        $type = VehicleTypes::where('name', 'like', "%".$postData['type']."%")->first();         
        if($type)
        {
            $vehicle->type = $type->id;
        }                        
        $store = Store::where('name', 'like', "%".$postData['store']."%")->first();
        if($store)
        {
            $vehicle->store = $store->id;
        }
        if(isset($postData['location']) && $postData['location'])
        {
            $location = Location::where('name', 'like', "%".$postData['location']."%")->first();
            if($location)
            {
                $vehicle->location = $location->id;
            }    
        }                                    
        $vehicle->power = $postData['power'];
        $vehicle->places = $postData['places'];
        $vehicle->color = $postData['color'];
        $vehicle->km = $postData['km'];
        $vehicle->cost = $postData['cost'];
        $vehicle->pvp = $postData['pvp'];
        $vehicle->doors = $postData['doors'];
        return $vehicle;
    }
    public function addAccesoryAction($request)
    {
        $postData = $request->getParsedBody(); 
        
        $getaccesory = json_decode($postData['vhaccesory']);           
        $accesory = Accesories::where('name', 'like', "%".$getaccesory->accesory."%")->first();        
        $vehicle_accesory = VehicleAccesories::where('vehicleId', '=', $getaccesory->vehicle_id)
                ->where('accesoryId', '=', $accesory->id)
                ->first();
             
        if($vehicle_accesory === null)
        {
            $vehicle_accesory = new VehicleAccesories();
        }        
        $vehicle_accesory->vehicleId = $getaccesory->vehicle_id;
        $vehicle_accesory->accesoryId = $accesory->id;        
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
        $accesory = Accesories::where('name', 'like', "%".$getaccesory->accesory."%")->first();               
        $vehicle_accesory = VehicleAccesories::where('vehicleId', '=', $getaccesory->vehicle_id)
                ->where('accesoryId', '=', $accesory->id)
                ->first();        
        if($vehicle_accesory !== null)
        {
            $responseMessage = 'Accesory Deleted';
            $vehicle_accesory->delete();
        }             
        $response = new JsonResponse($responseMessage);
        return $response;
    }
    public function importVehiclesExcel()
    {
        setlocale(LC_ALL, 'es_ES');
        $responseMessage = null;
        $reader = new Xls();
        $reader->setLoadSheetsOnly('GENERAL');
        $spreadSheet = $reader->load('VEHICULOS 01-08-19 UBICACIONES.xls');
        $vehiculos = $spreadSheet->getActiveSheet()->toArray();
        if($this->importVehicleBrands())
        {
            $reponseMessage = $this->importVehicleBrands();
        }
        if($this->importVehicleModels())
        {
            $reponseMessage = $this->importVehicleModels();
        }
        if($this->importVehiclesStores())
        {
            $responseMessage = $this->importVehiclesStores();
        }
        if($this->importVehicleTypes())
        {
            $responseMessage = $this->importVehicleTypes();
        }
        try{
            for($i = 1; $i < intval(count($vehiculos)); $i++)
            {
                $vehiculo = new Vehicle();                              
                $vehiculo->brand = $vehiculos[$i][1];                             
                $vehiculo->model = $vehiculos[$i][3];
                $vehiculo->description = $vehiculos[$i][7];
                $vehiculo->plate = $vehiculos[$i][6];
                $vehiculo->vin = null;
                $time = strtotime($vehiculos[$i][11]);                
                $vehiculo->registryDate = date('Y/m/d', $time);                
                $vehiculo->store = $vehiculos[$i][9];
                $vehiculo->location = 0;
                $vehiculo->type = $vehiculos[$i][5];
                $vehiculo->color = null;
                $vehiculo->places = 0;
                $vehiculo->doors = 0;
                $vehiculo->power = 0;
                $vehiculo->km = $vehiculos[$i][10];
                $vehiculo->cost = 0;
                $vehiculo->pvp = $this->tofloat($vehiculos[$i][13]);                
                $vehiculo->save(); 
                $responseMessage = 'Saved';               
            }
        } 
        catch (QueryException $ex) 
        {
            $responseMessage = $this->errorService->getError($ex);            
        }        
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
    public function importVehicleBrands(){
        setLocale(LC_ALL, 'es_ES');
        $responseMessage = null;
        $reader = new Xls();
        $reader->setLoadSheetsOnly('MARCAS'); 
        $spreadSheet = $reader->load('VEHICULOS 01-08-19 UBICACIONES.xls');
        $marcas = $spreadSheet->getActiveSheet()->toArray();
        try{
            for($i = 1; $i < intval(count($marcas)); $i++)
            {
                 $marca = new Brand();
                 $marca->name = $marcas[$i][0];
                 $marca->save();                 
            }           
        } catch (QueryException $ex) {
            $responseMessage = $this->errorService->getError($ex);
        }
        return $responseMessage;
    }
    public function importVehicleModels(){
        setLocale(LC_ALL, 'es_ES');
        $responseMessage = null;
        $reader = new Xls();
        $reader->setLoadSheetsOnly('MODELOS'); 
        $spreadSheet = $reader->load('VEHICULOS 01-08-19 UBICACIONES.xls');
        $modelos = $spreadSheet->getActiveSheet()->toArray();
//        var_dump($modelos);die();
        try{
            for($i = 1; $i < intval(count($modelos)); $i++)
            {
                 $modelo = new ModelVh();                                  
                 $modelo->name = $modelos[$i][0];
                 $modelo->brandId = $modelos[$i][3];
                 $modelo->save();                 
            }           
        } catch (QueryException $ex) {
            $responseMessage = $this->errorService->getError($ex);
        }
        return $responseMessage;
    }
    public function importVehicleTypes(){
        setLocale(LC_ALL, 'es_ES');
        $responseMessage = null;
        $reader = new Xls();
        $reader->setLoadSheetsOnly('TIPOS'); 
        $spreadSheet = $reader->load('VEHICULOS 01-08-19 UBICACIONES.xls');
        $types = $spreadSheet->getActiveSheet()->toArray();
//         var_dump($modelos);die();
        try{
            for($i = 1; $i < intval(count($types)); $i++)
            {
                 $type = new VehicleTypes();                       
                 $type->name = $types[$i][0];
                 $type->save();                 
            }           
        } catch (QueryException $ex) {
            $responseMessage = $this->errorService->getError($ex);
        }
        return $responseMessage;
    }
    public function importVehiclesStores(){
        setLocale(LC_ALL, 'es_ES');
        $responseMessage = null;
        $reader = new Xls();
        $reader->setLoadSheetsOnly('ALMACENES'); 
        $spreadSheet = $reader->load('VEHICULOS 01-08-19 UBICACIONES.xls');
        $stores = $spreadSheet->getActiveSheet()->toArray();
//         var_dump($modelos);die();
        try{
            for($i = 1; $i < intval(count($stores)); $i++)
            {
                 $store = new Store();                       
                 $store->name = $stores[$i][0];
                 $store->save();                 
            }           
        } catch (QueryException $ex) {
            $responseMessage = $this->errorService->getError($ex);
        }
        return $responseMessage;
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
