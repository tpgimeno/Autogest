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
use function GuzzleHttp\json_decode;

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
        $this->itemsList = array('id', 'brand', 'model', 'km', 'pvp');
        $this->properties = $this->vehicleService->getModelProperties($this->model);
    }    
    public function getIndexAction($request) {
        return $this->getBaseIndexAction($request, $this->model, null);
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
            'works' => $works);
        if($request->getMethod() == 'POST') {
            $postData = $request->getParsedBody();            
            $vehicleValidator = v::key('plate', v::stringType()->notEmpty());                       
            try{
                $vehicleValidator->assert($postData); // true                  
            }catch(Exception $e) {                
                $responseMessage = $e->getMessage();
            } 
            var_dump("Prueba");die();
            return $this->getBasePostDataAction($request, $this->model, $iterables, $responseMessage);
        }else{
            return $this->getBaseGetDataAction($request, $this->model, $iterables);
        }       
    }      
    public function addAccesoryAction($request) {
        $postData = $request->getParsedBody();         
        $getAccesory = json_decode($postData['vhaccesory']);           
        $responseMessage = $this->vehicleService->addVehicleAccesory($getAccesory);
        $response = new JsonResponse($responseMessage);
        return $response;
    }
    public function deleteAccesoryAction($request){        
        $postData = $request->getParsedBody();
        $responseMessage = $this->vehicleService->deleteVehicleAccesory($postData);   
        $response = new JsonResponse($responseMessage);
        return $response;
    }
    public function searchComponentAction($request){
        $searchString = null;
        $postData = $request->getParsedBody();
        if(isset($postData['searchFilter'])){
            $searchString = $postData['searchFilter'];
        }        
        $components = $this->vehicleService->searchComponent($searchString);
        $response = new JsonResponse($components);        
        return $response;       
    }    
    public function addComponentAction($request){       
        $postData = $request->getParsedBody();
        $responseMessage = $this->vehicleService->addVehicleComponent($postData);
        $response = new JsonResponse($responseMessage);
        return $response;        
    }
    public function editComponentAction($request){
        $this->vehicleService->deleteVehicleComponent($request->getQueryParams());       
        return $this->getVehicleDataAction($request);        
    }
    public function delComponentAction($request){        
        $this->vehicleService->deleteVehicleComponent($request->getQueryParams());             
        return $this->getVehicleDataAction($request);       
    }    
    public function searchSupplyAction($request){
        $searchString = null;
        $postData = $request->getParsedBody();
        if(isset($postData['searchFilter'])){
            $searchString = $postData['searchFilter'];
        }
        $supplies = $this->vehicleService->searchSupply($searchString);    
        $response = new JsonResponse($supplies);        
        return $response;       
    }    
    public function addSupplyAction($request){        
        $postData = $request->getParsedBody();
        $responseMessage = $this->vehicleService->addVehicleSupply($postData);
        $response = new JsonResponse($responseMessage);
        return $response;        
    }    
    public function delSupplyAction($request){               
        $this->vehicleService->deleteVehicleSupply($request->getQueryParams());
        return $this->getVehicleDataAction($request);        
    }
    public function editSupplyAction($request) {
        $this->vehicleService->deleteVehicleSupply($request->getQueryParams());
        return $this->getVehicleDataAction($request);        
    }
    public function getSelectedSupply($params){
        $selected_supply = null;
        if(isset($params['supplyId'])&& $params['supplyId']){
            $selected_supply = Supplies::find($params['supplyId'])->first();                        
        }
        return $selected_supply;
    }
    public function reloadModelsAction($request){
        $models = $this->vehicleService->reloadModels($request->getParsedBody());        
        $response = new JsonResponse($models);
        return $response;
    }
    public function reloadLocationsAction($request){
        $locations = $this->vehicleService->reloadLocations($request->getParsedBody());
        $response = new JsonResponse($locations);
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
