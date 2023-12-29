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
use App\Models\Customer;
use App\Models\Location;
use App\Models\ModelVh;
use App\Models\Providor;
use App\Models\Sellers;
use App\Models\Store;
use App\Models\Supplies;
use App\Models\Vehicle;
use App\Models\VehicleTypes;
use App\Models\Works;
use App\Reports\Vehicles\VehiclesReport;
use App\Services\Vehicle\ImportVehiclesService;
use App\Services\Vehicle\VehicleService;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\QueryException;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Diactoros\ServerRequest;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use Respect\Validation\Validator as v;

/**
 * Description of VehiclesController
 *
 * @author tonyl
 */
class VehicleController extends BaseController {
    
    protected $vehicleService;   
    protected $importVehicleService;
    
    public function __construct(VehicleService $vehicleService, ImportVehiclesService $importVehiclesService) {
        parent::__construct();        
        $this->vehicleService = $vehicleService;
        $this->importVehicleService = $importVehiclesService;
        $this->model = new Vehicle();
        $this->route = 'vehicles';
        $this->titleList = 'Vehículos';
        $this->titleForm = 'Vehículo';
        $this->labels = $this->vehicleService->getLabelsArray(); 
        $this->itemsList = array('id', 'brand_id', 'model_id', 'km', 'vehiclePvp');
        $this->properties = $this->vehicleService->getModelProperties($this->model);        
        $this->assetsNames = ['vehicle_accesories'];
    }    
    public function getIndexAction($request) {
        $values = $this->vehicleService->list();
        return $this->getBaseIndexAction($request, $this->model, $values);
    }    
        
    public function getVehicleDataAction($request) {                
        $responseMessage = null;         
        $brands = $this->vehicleService->getAllRegisters(new Brand());
        $models = $this->vehicleService->getAllRegisters(new ModelVh());
        $sellers = $this->vehicleService->getAllRegisters(new Sellers());
        $stores = $this->vehicleService->getAllRegisters(new Store());
        $locations = $this->vehicleService->getAllRegisters(new Location());
        $types = $this->vehicleService->getAllRegisters(new VehicleTypes());
        $providors = $this->vehicleService->getAllRegisters(new Providor());
        $customers = $this->vehicleService->getAllRegisters(new Customer());
        $components = $this->vehicleService->getAllRegisters(new Components());
        $supplies = $this->vehicleService->getAllRegisters(new Supplies());
        $works = $this->vehicleService->getAllRegisters(new Works());
        $accesories = $this->vehicleService->getAllRegisters(new Accesories()); 
        $vehicle_accesories = $this->vehicleService->getVehicleAccesories($request);
        $vehicle_components = $this->vehicleService->getVehicleComponents($request);   
        $vehicle_supplies = $this->vehicleService->getVehicleSupplies($request);
        $vehicle_works = $this->vehicleService->getVehicleWorks($request);
        $iterables = array('brand_id' => $brands,
            'model_id' => $models,
            'seller_id' => $sellers,
            'store_id' => $stores,
            'location_id' => $locations,
            'type_id' => $types,
            'providor_id' => $providors,
            'customer_id' => $customers,
            'accesories' => $accesories,
            'components' => $components,
            'supplies' => $supplies,
            'works' => $works,
            'vehicle_accesories' => $vehicle_accesories,
            'vehicle_components' => $vehicle_components,
            'vehicle_supplies' => $vehicle_supplies,
            'vehicle_works' => $vehicle_works,
            'vehicle_component_labels' => ['vehiclecomponent_id' => 'vehiclecomponent_id','mader' => 'mader','ref' => 'ref','name' => 'name','cantity' => 'cantity','pvp' => 'pvp','total' => 'total'],
            'vehicle_supply_labels' => ['vehiclesupply_id' => 'vehiclesupply_id','mader' => 'mader','ref' => 'ref','name' => 'name','cantity' => 'cantity','pvp' => 'pvp','total' => 'total'],
            'vehicle_work_labels' => ['vehiclework_id' => 'vehiclework_id','ref' => 'ref','name' => 'name','cantity' => 'cantity','pvp' => 'pvp','total' => 'total'],
            'component_functions' => ['set' => 'setComponent', 'delete' => 'delVehicleComponent'],
            'supply_functions' => ['set' => 'setSupply', 'delete' => 'delVehicleSupply'],
            'work_functions' => ['set' => 'setWork', 'delete' => 'delVehicleWork'],
            'assets_prices' => ['1' => 'baseComponents','2' => 'Base Componentes','3' => 'tvaComponents','4' => 'Iva','5' => 'totalComponents', '6' => 'Total Componentes','7' => 'baseSupplies', '8' => 'Base Recambios', '9' => 'tvaSupplies', '10' => 'Iva Recambios', '11' => 'totalSupplies', '12' => 'Total Recambios', '13' => 'baseWorks', '14' => 'Base Trabajos', '15' => 'tvaWorks', '16' => 'Iva Trabajos', '17' => 'totalWorks', '18' => 'Total Trabajos'],
            'assets_labels' => ['id' => 'id', 'ref' => 'ref','name' => 'name','pvp' => 'pvp'],
            'setComponentsUrl' => "Intranet/Vehicles/components/set",
            'parent_id' => 'vehicle_id',
            'object_id' => ['1' => 'component_id','2' => 'supply_id','3' => 'work_id'],
            'modals_functions' => ['setComponent' => 'setComponent', 'saveComponent' => 'saveVehicleComponent()' ,'setSupply' => 'setSupply', 'saveSupply' => 'saveVehicleSupply()','setWork' => 'setWork','saveWork' => 'saveVehicleWork()'],
            'forms' => ['1' => 'vehicle_component_form','2' => 'vehicle_supply_form','3' => 'vehicle_work_form']);
        if($request->getMethod() == 'POST') {
            $postData = $request->getParsedBody();
            $postData = $this->getCheckboxes($postData);                     
            $vehicleValidator = v::key('plate', v::stringType()->notEmpty());                       
            try{
                $vehicleValidator->assert($postData); // true                  
            }catch(Exception $e) {                
                $responseMessage = $e->getMessage();
            }            
            return $this->getBasePostDataAction($postData, $this->model, $iterables, $responseMessage);
        }else{
            return $this->getBaseGetDataAction($request, $this->model, $iterables);
        }       
    }
    public function getCheckboxes($postData){
        $added_data = [];
        $position = 0;
        if(!isset($postData['secondKey'])){                
            $added_data['secondKey'] = 0;
            $position = 1;
        }
        if(!isset($postData['rebu'])){
            $added_data['rebu'] = 0;
            if(count($added_data) > 0){
                $position = 2;
            }
        }
        $postData = $this->joinArrayPostData($postData, $added_data, $position);
        return $postData;
    }
    
    public function joinArrayPostData($postData, $added_data, $position){
        $array_head = array_slice($postData, 0,array_search('service', array_keys($postData))+$position,true);
        $array_cue = array_slice($postData, array_search('service', array_keys($postData))+$position,null,true);        
        $array_head = array_merge($array_head, $added_data);
        $postData = array_merge($array_head, $array_cue);          
        return $postData;
    }
    public function getAccesoryAction($request){
        $postData = $request->getParsedBody();
        $accesories = $this->vehicleService->getVehicleAccesoriesAjax($postData['id']);        
        $response = new JsonResponse($accesories);
        return $response;
    }
    public function addAccesoryAction($request){         
        $postData = $request->getParsedBody();
        $responseMessage = $this->vehicleService->addVehicleAccesoryAjax($postData);
        $response = new JsonResponse($responseMessage);
        return $response;        
    }
    
    public function deleteAccesoryAction($request){        
        $postData = $request->getParsedBody();
        $responseMessage = $this->vehicleService->delVehicleAccesoryAjax($postData);
        $response = new JsonResponse($responseMessage);
        return $response;  
    }
    
    public function getVehicleComponentsAction($request){        
        $components = $this->vehicleService->getVehicleComponents($request);
        $response = new JsonResponse($components);
        return $response;
    }
    
    public function addVehicleComponentAction($request){
        $postData = $request->getParsedBody();       
        $responseMessage = $this->vehicleService->addVehicleComponentAjax($postData);
        $response = new JsonResponse($responseMessage);
        return $response;
    }
    
    public function delVehicleComponentAction($request){
        $postData = $request->getParsedBody();        
        $responseMessage = $this->vehicleService->delVehicleComponentAjax($postData);
        $response = new JsonResponse($responseMessage);
        return $response;
    }
    
    public function getVehicleSuppliesAction($request){
        $supplies = $this->vehicleService->getVehicleSupplies($request);
        $response = new JsonResponse($supplies);
        return $response;
    }
    public function addVehicleSupplyAction($request){
        $postData = $request->getParsedBody();       
        $responseMessage = $this->vehicleService->addVehicleSupplyAjax($postData);
        $response = new JsonResponse($responseMessage);
        return $response;
    }
    public function delVehicleSupplyAction($request){
        $postData = $request->getParsedBody(); 
        
        $responseMessage = $this->vehicleService->delVehicleSupplyAjax($postData);
        $response = new JsonResponse($responseMessage);
        return $response;
    }
    
    public function getVehicleWorksAction($request){
        $works = $this->vehicleService->getVehicleWorks($request);
        $response = new JsonResponse($works);
        return $response;
    }
    public function addVehicleWorkAction($request){
        
        $postData = $request->getParsedBody();       
        $responseMessage = $this->vehicleService->addVehicleWorkAjax($postData);
        $response = new JsonResponse($responseMessage);
        return $response;
    }
    public function delVehicleWorkAction($request){
        $postData = $request->getParsedBody();        
        $responseMessage = $this->vehicleService->delVehicleWorkAjax($postData);
        $response = new JsonResponse($responseMessage);
        return $response;
    }
    
    public function importVehiclesExcel() {
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
    public function deleteAction(ServerRequest $request) {         
        $this->vehicleService->deleteRegister(new Vehicle(), $request->getQueryParams('id'));               
        return new RedirectResponse($this->list);
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
