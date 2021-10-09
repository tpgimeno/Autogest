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
use App\Models\Components;
use App\Models\Location;
use App\Models\ModelVh;
use App\Models\Store;
use App\Models\Supplies;
use App\Models\Vehicle;
use App\Models\VehicleAccesories;
use App\Models\VehicleComponents;
use App\Models\VehicleSupplies;
use App\Models\VehicleTypes;
use App\Reports\Vehicles\VehiclesReport;
use App\Services\Vehicle\VehicleService;
use Illuminate\Database\QueryException;
use Illuminate\Database\Capsule\Manager as DB;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Diactoros\ServerRequest;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use Respect\Validation\Validator as v;
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
        $vehicleSelected = $this->setVehicle($request);
        $params = $request->getQueryParams();
        return $this->renderAgain($params, $vehicleSelected, $responseMessage, null);
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
            $vehicleSelected = Vehicle::find($params['id']);
        }        
        return $vehicleSelected;        
    }  
    public function setBrand($vehicleSelected){
        $brandSelected = null; 
        
        if($vehicleSelected)
        {
            $brandSelected = Brand::find($vehicleSelected->brand)->name;            
        }
        return $brandSelected;
    }
    public function findBrand($vehicleSelected){
        $brand = Brand::find($vehicleSelected->brand);
        if($brand){
            return true;
        }
        else{
            return false;
        }
    }
    public function setModel($vehicleSelected){
        $modelSelected = null;
        if($this->findBrand($vehicleSelected)){
            $modelSelected = ModelVh::find($vehicleSelected->model)->name;            
        }
        return $modelSelected;
    }
    public function setStore($vehicleSelected){
        $store_selected = null;
        if($vehicleSelected && $vehicleSelected->store > 0)
        {
            $store_selected = Store::find($vehicleSelected->store)->name;            
        } 
        
        return $store_selected;        
    }
    public function setLocation($vehicleSelected){
        $location_selected = null; 
        
        if($this->findLocation($vehicleSelected)){
            $location_selected = Location::find($vehicleSelected->location)->name;             
        }
        return $location_selected;
    }
    public function findLocation($vehicleSelected){
        $location = Location::find($vehicleSelected->location);
        if($location){
            return true;
        }
        else{
            return false;
        }
    }
    public function setVehicleType($vehicleSelected){
        $typeSelected = null;
        
        if($vehicleSelected)
        {            
            $typeSelected = VehicleTypes::find($vehicleSelected->type)->name;            
        }
        return $typeSelected;
    }
    public function setAccesories($vehicleSelected){
        $selected_accesories = null;
        if($vehicleSelected)
        {                          
            $selected_accesories = DB::table('vehicleAccesories')
                    ->join('accesories', 'vehicleAccesories.accesoryId', '=', 'accesories.id')
                    ->select('vehicleAccesories.accesoryId','vehicleAccesories.id', 'accesories.keystring', 'accesories.name')
                    ->where('vehicleAccesories.vehicleId', '=', $vehicleSelected->id)
                    ->get()->toArray();            
        }
        return $selected_accesories;
    }
    public function setVehicleComponents($vehicleSelected){
        $selectedComponents = null;        
        if($vehicleSelected){
            $selectedComponents = DB::table('vehicleComponents')
                    ->join('components', 'vehicleComponents.componentId', '=', 'components.id')
                    ->join('maders', 'components.mader', '=', 'maders.id')
                    ->select('vehicleComponents.componentId', 'vehicleComponents.vehicleId as vehicleId', 'vehicleComponents.cantity as cantity', 'components.name', 'components.ref', 'components.serialNumber', 'components.mader', 'vehicleComponents.pvp')
                    ->where('vehicleComponents.vehicleId', '=', $vehicleSelected->id)                    
                    ->get();                    
        }
        return $selectedComponents;
    }
    
    public function setVehicleSupplies($vehicleSelected){
        $selectedSupply = null;
        if($vehicleSelected){
            $selectedSupply = DB::table('vehicleSupplies')
                    ->join('supplies', 'vehicleSupplies.supplyId', '=', 'supplies.id')
                    ->select('vehicleSupplies.supplyId', 'supplies.name', 'supplies.ref', 'supplies.mader', 'supplies.pvp')
                    ->where('vehicleSupplies.vehicleId', '=', $vehicleSelected->id)                    
                    ->get();
        }
        return $selectedSupply;
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
        $vehicle_accesory = VehicleAccesories::where('vehicleId', '=', $getaccesory->vehicleId)
                ->where('accesoryId', '=', $accesory->id)
                ->first();
             
        if($vehicle_accesory === null)
        {
            $vehicle_accesory = new VehicleAccesories();
        }        
        $vehicle_accesory->vehicleId = $getaccesory->vehicleId;
        $vehicle_accesory->accesoryId = $accesory->id;        
        $vehicle_accesory->save();
        $responseMessage = 'Accesory Saved';
        $response = new JsonResponse($responseMessage);
        return $response;
    }
    public function deleteAccesoryAction($request){
        $responseMessage = null;
        $vehicle_accesory = null;
        $postData = $request->getParsedBody();
        if($postData){
            $getaccesory = json_decode($postData['vhaccesory']);        
            $accesory = Accesories::where('name', 'like', "%".$getaccesory->accesory."%")->first();               
            $vehicle_accesory = VehicleAccesories::where('vehicleId', '=', $getaccesory->vehicleId)
                    ->where('accesoryId', '=', $accesory->id)
                    ->first();
        }
        if($vehicle_accesory !== null){
            $responseMessage = 'Accesory Deleted';
            $vehicle_accesory->delete();
        }             
        $response = new JsonResponse($responseMessage);
        return $response;
    }
    public function searchComponentAction($request){
        $searchString = null;
        $postData = $request->getParsedBody();
        if(isset($postData['searchFilter'])){
            $searchString = $postData['searchFilter'];
        }        
        if($searchString == null){
            $components = Components::All();
        }
        else{
            $components = DB::table('components')
                 ->join('maders', 'components.mader', '=', 'maders.id')
                 ->select('components.id', 'components.ref', 'components.serialNumber', 'components.pvp')
                 ->where('components.id', 'like', "%".$searchString."$")
                 ->orWhere('components.ref', 'like', "$".$searchString."$")
                 ->orWhere('maders.name', 'like', "$".$searchString."$")
                 ->orWhere('components.serialNumber', 'like', "$".$searchString."$")
                 ->whereNull('deleted_at')
                 ->get();
        }
        $response = new JsonResponse($components);        
        return $response;       
    }
    public function selectComponentAction($request)
    {
        $responseMessage = null;
        $vehicleSelected = null;
        $selected_tab = 'accesories';
        try{
            $params = $request->getQueryParams();
        }catch(Exception $e){
            $responseMessage = $e->getMessage();
        }
        if(isset($params['vehicleId'])&& $params['vehicleId']){
            $vehicleSelected = Vehicle::find($params['vehicleId'])->first();
        }
        return $this->renderAgain($params, $vehicleSelected, $responseMessage, $selected_tab);        
    }
    public function addComponentAction($request){
       $responseMessage = null;
        $postData = $request->getParsedBody();
        $data = json_decode($postData['component']);        
        $vehicleComponent = new VehicleComponents();
        $temp_component = VehicleComponents::where('componentId', '=', $data->componentId)
                ->where('vehicleId', '=', $data->vehicleId)
                ->first();
        if($temp_component){
            $vehicleComponent = $temp_component;
        }              
        $vehicleComponent->componentId = $data->componentId;
        $vehicleComponent->vehicleId = $data->vehicleId;
        $vehicleComponent->cantity = $data->cantity;
        $vehicleComponent->price = $data->price;
        if($temp_component){
            $vehicleComponent->update();
            $responseMessage = 'Component Updated';
        }
        else{
            $vehicleComponent->save();
            $responseMessage = 'Component Saved';
        }
        $response = new JsonResponse($responseMessage);
        return $response;        
    }    
    public function delComponentAction($request){
        $responseMessage = 'Componente eliminado';
        $selected_tab = 'accesories';
        $params = $request->getQueryParams();        
        $component = VehicleComponents::where('componentId', '=', $params['componentId'])
                ->where('vehicleId', '=', $params['vehicleId']);
        if($component)
        {
            $component->delete();
        }
        if(isset($params['vehicleId'])&& $params['vehicleId']){
            $vehicleSelected = Vehicle::find($params['vehicleId'])->first();
        }
        return $this->renderAgain($params, $vehicleSelected, $responseMessage, $selected_tab);        
    }
    public function editComponentAction($request) {
        $responseMessage = 'Editando Component';
        $selected_tab = 'accesories';
        $params = $request->getQueryParams();        
        $component = DB::table('vehicleComponents')                
                ->join('components', 'vehicleComponents.componentId', '=', 'components.id')
                ->join('maders', 'components.mader', '=', 'maders.id')
                ->select('vehicleComponents.id', 'vehicleComponents.vehicleId', 'vehicleComponents.componentId', 'components.ref as reference', 'maders.name as mader', 'components.name as name', 'vehicleComponents.cantity as cantity', 'vehicleComponents.pvp as price')
                ->where('vehicleComponents.vehicleId', '=', $params['vehicleId'])
                ->where('vehicleComponents.componentId', '=', $params['componentId'])
                ->first();        
        if($component)
        {
            $array = (['component_price' => $component->price ,'component_cantity' => $component->cantity]);
            $params = array_merge($params, $array);
            $editComponent = VehicleComponents::find($component->id);            
            if($editComponent)
            {
                $editComponent->delete();
            } 
        }  
        
        if(isset($params['vehicleId'])&& $params['vehicleId']){
            $vehicleSelected = Vehicle::find($params['vehicleId'])->first();
        }        
        return $this->renderAgain($params, $vehicleSelected, $responseMessage, $selected_tab);        
    }
    public function getSelectedComponent($params){
        $selected_component = null;
        if(isset($params['componentId'])&& $params['componentId']){
            $selected_component = Components::find($params['componentId'])->first();                       
        }
        return $selected_component;
    }
    public function searchSupplyAction($request){
        $searchString = null;
        $postData = $request->getParsedBody();
        if(isset($postData['searchFilter'])){
            $searchString = $postData['searchFilter'];
        }        
        if($searchString == null){
            $supplies = Supplies::All();
        }
        else{
            $supplies = DB::table('supplies')
                 ->join('maders', 'supplies.mader', '=', 'maders.id')
                 ->select('supplies.id', 'supplies.ref', 'supplies.serialNumber', 'supplies.pvp')
                 ->where('supplies.id', 'like', "%".$searchString."$")
                 ->orWhere('supplies.ref', 'like', "$".$searchString."$")
                 ->orWhere('maders.name', 'like', "$".$searchString."$")
                 ->orWhere('supplies.serialNumber', 'like', "$".$searchString."$")
                 ->whereNull('deleted_at')
                 ->get();
        }
        $response = new JsonResponse($supplies);        
        return $response;       
    }
    public function selectSupplyAction($request)
    {
        $responseMessage = null;
        $vehicleSelected = null;
        $selected_tab = 'accesories';
        try{
            $params = $request->getQueryParams();
        }catch(Exception $e){
            $responseMessage = $e->getMessage();
        }       
        if(isset($params['vehicleId'])&& $params['vehicleId']){
            $vehicleSelected = Vehicle::find($params['vehicleId'])->first();
        }        
        return $this->renderAgain($params, $vehicleSelected, $responseMessage, $selected_tab);        
    }
    public function addSupplyAction($request){
        $responseMessage = null;
        $postData = $request->getParsedBody();
        $data = json_decode($postData['supply']);        
        $vehicleSupply = new VehicleSupplies();
        $temp_supply = VehicleSupplies::where('supplyId', '=', $data->supplyId)
                ->where('vehicleId', '=', $data->vehicleId)
                ->first();
        if($temp_supply){
            $vehicleSupply = $temp_supply;
        }              
        $vehicleSupply->supplyId = $data->supplyId;
        $vehicleSupply->vehicleId = $data->vehicleId;
        $vehicleSupply->cantity = $data->cantity;
        $vehicleSupply->price = $data->price;
        if($temp_supply){
            $vehicleSupply->update();
            $responseMessage = 'Supply Updated';
        }
        else{
            $vehicleSupply->save();
            $responseMessage = 'Supply Saved';
        }
        $response = new JsonResponse($responseMessage);
        return $response;        
    }    
    public function delSupplyAction($request){
        $responseMessage = 'Recambio eliminado';
        $selected_tab = 'accesories';
        $params = $request->getQueryParams();        
        $supply = VehicleSupplies::where('supplyId', '=', $params['supplyId'])
                ->where('vehicleId', '=', $params['vehicleId'])
                ->first();
        
        if($supply)
        {
            $supply->delete();
        }
        if(isset($params['vehicleId'])&& $params['vehicleId']){
            $vehicleSelected = Vehicle::find($params['vehicleId'])->first();
        }
        return $this->renderAgain($params, $vehicleSelected, $responseMessage, $selected_tab);        
    }
    public function editSupplyAction($request) {
        $responseMessage = 'Editando Supply';
        $selected_tab = 'accesories';
        $params = $request->getQueryParams();        
        $supply = DB::table('vehicleSupplies')                
                ->join('supplies', 'vehicleSupplies.supplyId', '=', 'supplies.id')
                ->join('maders', 'supplies.mader', '=', 'maders.id')
                ->select('vehicleSupplies.id', 'vehicleSupplies.vehicleId', 'vehicleSupplies.supplyId', 'supplies.ref as reference', 'maders.name as mader', 'supplies.name as name', 'vehicleSupplies.cantity as cantity', 'vehicleSupplies.price as price')
                ->where('vehicleSupplies.vehicleId', '=', $params['vehicleId'])
                ->where('vehicleSupplies.supplyId', '=', $params['supplyId'])
                ->first();        
        if($supply)
        {
            $array = (['supply_price' => $supply->price ,'supply_cantity' => $supply->cantity]);
            $params = array_merge($params, $array);
            $editSupply = VehicleSupplies::find($supply->id)->first();            
            if($editSupply)
            {
                $editSupply->delete();
            } 
        }        
        if(isset($params['vehicleId'])&& $params['vehicleId']){
            $vehicleSelected = Vehicle::find($params['vehicleId'])->first();           
        }        
        return $this->renderAgain($params, $vehicleSelected, $responseMessage, $selected_tab);        
    }
    public function getSelectedSupply($params){
        $selected_supply = null;
        if(isset($params['supplyId'])&& $params['supplyId']){
            $selected_supply = Supplies::find($params['supplyId'])->first();                        
        }
        return $selected_supply;
    }
    public function importVehiclesExcel()
    {
        setlocale(LC_ALL, 'es_ES');
        $responseMessage = null;
        $reader = new Xls();
        $reader->setLoadSheetsOnly('VEHICULOS');
        $spreadSheet = $reader->load('vehiculos.xls');        
        $vehiculos = $spreadSheet->getActiveSheet()->toArray();
        try{
            $responseMessage . " - ". $this->importVehicleBrands();
        }catch(Exception $e){
            $responseMessage = $e->getMessage();
        }        
        try{
            $responseMessage ." - ". $this->importVehicleModels();
        }catch(Exception $e){
            $responseMessage = $e->getMessage();
        }        
        try{
            $responseMessage . " - " . $this->importVehiclesStores();
        }catch(Exception $e){
            $responseMessage = $e->getMessage();
        }
        try
        {
            $responseMessage . " - " . $this->importVehicleTypes();
        }catch(Exception $e){
            $responseMessage = $e->getMessage();
        }        
//        var_dump($vehiculos);die();       
        try{
            for($i = 1; $i < intval(count($vehiculos)); $i++)
            {
                $vehiculo = new Vehicle();                
                $vehiculo->brand = $vehiculos[$i][3];                             
                $vehiculo->model = $vehiculos[$i][5];                
                $vehiculo->description = $vehiculos[$i][6];
                $vehiculo->plate = $vehiculos[$i][0];
                $vehiculo->vin = $vehiculos[$i][1];
                $time = null;
                if($vehiculos[$i][11])
                {
                    $time = strtotime($vehiculos[$i][11]); 
                }               
                $vehiculo->registryDate = date('Y-m-d', $time);               
                $vehiculo->store = $vehiculos[$i][13];
                $vehiculo->location = 0;
                $vehiculo->type = $vehiculos[$i][17];
                $vehiculo->color = $vehiculos[$i][9];
                $vehiculo->places = null;
                $vehiculo->doors = $vehiculos[$i][8];
                $vehiculo->power = $vehiculos[$i][7];
                $vehiculo->km = $vehiculos[$i][10];
                $vehiculo->cost = $this->tofloat($vehiculos[$i][19]);
                $vehiculo->pvp = $this->tofloat($vehiculos[$i][20]);                           
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
        $spreadSheet = $reader->load('vehiculos.xls');
        $marcas = $spreadSheet->getActiveSheet()->toArray();
//        var_dump($marcas);die();
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
        $spreadSheet = $reader->load('vehiculos.xls');
        $modelos = $spreadSheet->getActiveSheet()->toArray();
//        var_dump($modelos);die();
        try{
            for($i = 1; $i < intval(count($modelos)); $i++)
            {
                 $modelo = new ModelVh();                                  
                 $modelo->name = $modelos[$i][2];
                 $modelo->brandId = $modelos[$i][1];
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
        $spreadSheet = $reader->load('vehiculos.xls');         
        $types = $spreadSheet->getActiveSheet()->toArray();
//        var_dump($types);die(); 
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
        $reader->setLoadSheetsOnly('UBICACIONES'); 
        $spreadSheet = $reader->load('vehiculos.xls');
        $stores = $spreadSheet->getActiveSheet()->toArray();
//         var_dump($stores);die();
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
        return new RedirectResponse('/Intranet/vehicles/list');
    }
    public function renderAgain($params, $vehicleSelected, $responseMessage, $selected_tab){
        $brandSelected = null;
        $modelSelected = null;
        $storeSelected = null;
        $locationSelected = null;
        $selectedAccesories = null;
        $typeSelected = null;
        $vehicleComponents = null;
        $vehicleSupplies = null;  
        $editPriceComponent = null;
        $editCantityComponent = null;
        $editPriceSupply = null;
        $editCantitySupply = null;
        if($vehicleSelected)
        {
            $brandSelected = $this->setBrand($vehicleSelected);
            $modelSelected = $this->setModel($vehicleSelected);
            $storeSelected = $this->setStore($vehicleSelected);
            $locationSelected = $this->setLocation($vehicleSelected);
            $selectedAccesories = $this->setAccesories($vehicleSelected);
            $typeSelected = $this->setVehicleType($vehicleSelected);
            $vehicleComponents = $this->setVehicleComponents($vehicleSelected);
            $vehicleSupplies = $this->setVehicleSupplies($vehicleSelected);
        }
        $selected_component = $this->getSelectedComponent($params);   
        if(isset($params['componentId'])){
            if(isset($params['cantity'])&& $params['cantity']){                
            $editCantityComponent = $params['cantity'];
            }
            if(isset($params['price'])&& $params['price']){
                $editPriceComponent = $params['price'];
            }
            if(!$editPriceComponent && $selected_component){
                $editPriceComponent = $selected_component->pvp;
            }
        }        
        $selected_supply = $this->getSelectedSupply($params);
        if(isset($params['supplyId'])){
            if(isset($params['cantity'])&& $params['cantity']){          
            $editCantitySupply = $params['cantity'];
            }
            if(isset($params['price']) && $params['price']){
                $editPriceSupply = $params['price'];
            }
            if(!$editPriceSupply && $selected_supply){
                $editPriceSupply = $selected_supply->pvp;
            } 
        }             
        $accesories = Accesories::All();
        $brands = Brand::All();
        $models = ModelVh::All();
        $types = VehicleTypes::All();
        $stores = Store::All();
        $components = Components::All();
        $supplies = Supplies::All();
        $locations = Location::All();
        return $this->renderHTML('/vehicles/vehiclesForm.html.twig', [
            'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
            'responseMessage' => $responseMessage,
            'brands' => $brands,
            'models' => $models,
            'stores' => $stores,
            'components' => $components,
            'supplies' => $supplies,
            'locations' => $locations,
            'selected_tab' => $selected_tab,
            'store_selected' => $storeSelected,
            'location_selected' => $locationSelected,
            'types' => $types,
            'type_selected' => $typeSelected,
            'vehicle' => $vehicleSelected,
            'brand_selected' => $brandSelected,
            'model_selected' => $modelSelected,
            'accesories' => $accesories,
            'selected_accesories' => $selectedAccesories,
            'vehicle_components' => $vehicleComponents,
            'vehicle_supplies' => $vehicleSupplies,
            'selected_component' => $selected_component,
            'edit_price_component' => $editPriceComponent,
            'edit_cantity_component' => $editCantityComponent,
            'edit_price_supply' => $editPriceSupply,
            'edit_cantity_supply' => $editCantitySupply,
            'selected_supply' => $selected_supply
        ]);
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
    public function getVehiclesReportAction(){
        $vehicles = DB::table('vehicles')  
                ->join('brands', 'vehicles.brand', '=', 'brands.id')
                ->join('models', 'vehicles.model', '=', 'models.id')
                ->join('stores', 'vehicles.store', '=', 'stores.id')
                ->join('vehicleTypes', 'vehicles.type', '=', 'vehicleTypes.id')
                ->leftJoin('vehicleAccesories', 'vehicles.id', '=', 'vehicleAccesories.vehicleId')
                ->select('vehicles.id', 'brands.name as brand', 'models.name as model', 'vehicles.description', 'vehicles.plate', 'vehicles.vin', 'vehicles.registryDate', 'stores.name as store', 'vehicles.km', 'vehicles.cost', 'vehicles.pvp')               
                ->whereNull('vehicles.deleted_at')                
                ->get()->toArray();
        
        $newPostData = array_merge(['vehicles' => $vehicles ]);
        $report = new VehiclesReport();
        $report->AddPage();
        $report->Body($newPostData);        
        $report->Output();
    }
}
