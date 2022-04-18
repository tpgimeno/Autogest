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
use App\Models\VehicleSupplies;
use App\Models\VehicleTypes;
use App\Models\Works;
use App\Reports\Vehicles\VehiclesReport;
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
    protected $list = '/Intranet/vehicles/list';
    protected $tab = 'buys';
    protected $title = 'Vehículos';
    protected $save = "/Intranet/vehicles/save";
    protected $search = "/Intranet/vehicles/search";
    protected $formName = "vehiclesForm";
    protected $script = 'js/vehicles.js';
    protected $inputs = ['id' => ['id' => 'inputId', 'name' => 'id', 'title' => 'ID'],
        'registryDate' => ['id' => 'inputRegistryDate', 'name' => 'registryDate', 'title' => 'Fecha Matriculación'],
        'plate' => ['id' => 'inputPlate', 'name' => 'plate', 'title' => 'Matricula'],
        'vin' => ['id' => 'inputVin', 'name' => 'vin', 'title' => 'Bastidor'],
        'selectBrand' => ['id' => 'selectBrand', 'name' => 'brand', 'title' => 'Marca'],
        'selectModel' => ['id' => 'selectModel', 'name' => 'model', 'title' => 'Modelo'],
        'description' => ['id' => 'inputDescription', 'name' => 'description', 'title' => 'Descripción'],
        'selectVehicleType' => ['id' => 'selectType', 'name' => 'vehicleType', 'title' => 'Tipo de Vehículo'],
        'selectStore' => ['id' => 'selectStore', 'name' => 'store', 'title' => 'Almacén'],
        'selectLocation' => ['id' => 'selectLocation', 'name' => 'location', 'title' => 'Ubicación'],
        'km' => ['id' => 'inputKm', 'name' => 'km', 'title' => 'Kilómetros'],
        'power' => ['id' => 'inputPower', 'name' => 'power', 'title' => 'Potencia'],
        'places' => ['id' => 'inputPlaces', 'name' => 'places', 'title' => 'Plazas'],
        'doors' => ['id' => 'inputDoors', 'name' => 'doors', 'title' => 'Puertas'],
        'color' => ['id' => 'inputColor', 'name' => 'color', 'title' => 'Color'],
        'selectProvidor' => ['id' => 'inputProvidor', 'name' => 'providor', 'title' => 'Proveedor'],
        'arrival' => ['id' => 'inputArrival', 'name' => 'arrival', 'title' => 'Fecha de Llegada'],
        'dateBuy' => ['id' => 'inputDateBuy', 'name' => 'dateBuy', 'title' => 'Fecha de Compra'],
        'transference' => ['id' => 'inputTransference', 'name' => 'transference', 'title' => 'Quién realiza el cambio de nombre'],
        'service' => ['id' => 'inputService', 'name' => 'service', 'title' => 'Servicio al que se destina'],
        'secondKey' => ['id' => 'inputSecondKey', 'name' => 'secondKey', 'title' => '2ª Llave'],
        'rebu' => ['id' => 'inputRebu', 'name' => 'rebu', 'title' => 'R.E.B.U.'],
        'technicCard' => ['id' => 'inputTechnicCard', 'name' => 'technicCard', 'title' => 'Ficha Tecnica'],
        'permission' => ['id' => 'inputPermission', 'name' => 'permission', 'title' => 'Permiso Circulación'],
        'cost' => ['id' => 'inputCost', 'name' => 'cost', 'title' => 'Precio Compra'],
        'tvaBuy' => ['id' => 'inputTvaCost', 'name' => 'tvaCost', 'title' => 'Iva Compra'],
        'totalBuy' => ['id' => 'inputTotalCost', 'name' => 'totalCost', 'title' => 'Total Compra'],
        'seller' => ['id' => 'inputSeller', 'name' => 'seller', 'title' => 'Vendedor'],
        'customer' => ['id' => 'inputCustomer', 'name' => 'customer', 'title' => 'Cliente'],
        'appoint' => ['id' => 'inputAppoint', 'name' => 'appoint', 'title' => 'Reserva'],
        'dateSell' => ['id' => 'inputDateSell', 'name' => 'dateSell', 'title' => 'Fecha de Venta'],        
        'pvp' => ['id' => 'inputPvp', 'name' => 'pvp', 'title' => 'Precio Venta'],
        'tvaSell' => ['id' => 'inputTvaSell', 'name' => 'tvaSell', 'title' => 'Iva Venta'],
        'totalSell' => ['id' => 'inputTotalSell', 'name' => 'totalSell', 'title' => 'Total Venta'],
        'componentId' => ['id' => 'inputComponentId', 'name' => 'componentId', 'title' => 'ID'],
        'componentRef' => ['id' => 'inputComponentRef', 'name' => 'componentRef', 'title' => 'Referencia'],
        'componentName' => ['id' => 'inputComponentName', 'name' => 'componentName', 'title' => 'Nombre'],
        'componentPvp' => ['id' => 'inputComponentPrice', 'name' => 'componentPvp', 'title' => 'Precio'],
        'componentCantity' => ['id' => 'inputComponentCantity', 'name' => 'componentCantity', 'title' => 'Cantidad'],
        'componentTotal' => ['id' => 'inputComponentTotal', 'name' => 'componentTotal', 'title' => 'Total'],
        'componentsPrice' => ['id' => 'inputComponentsPrice', 'name' => 'componentsPrice', 'title' => 'Importe'],
        'componentsTva' => ['id' => 'inputComponentsTva', 'name' => 'componentsTva', 'title' => 'IVA'],
        'componentsTotal' => ['id' => 'inputComponentsTotal', 'name' => 'componentsTotal', 'title' => 'Total'],
        'supplyId' => ['id' => 'inputSupplyId', 'name' => 'supplyId', 'title' => 'ID'],
        'supplyRef' => ['id' => 'inputSupplyRef', 'name' => 'supplyRef', 'title' => 'Referencia'],
        'supplyName' => ['id' => 'inputSupplyName', 'name' => 'supplyName', 'title' => 'Nombre'],
        'supplyPvp' => ['id' => 'inputSupplyPrice', 'name' => 'supplyPvp', 'title' => 'Precio'],
        'supplyCantity' => ['id' => 'inputSupplyCantity', 'name' => 'supplyCantity', 'title' => 'Cantidad'],
        'supplyTotal' => ['id' => 'inputSupplyTotal', 'name' => 'supplyTotal', 'title' => 'Total'],
        'suppliesPrice' => ['id' => 'inputSuppliesPrice', 'name' => 'suppliesPrice', 'title' => 'Importe'],
        'suppliesTva' => ['id' => 'inputSuppliesTva', 'name' => 'suppliesTva', 'title' => 'IVA'],
        'suppliesTotal' => ['id' => 'inputSuppliesTotal', 'name' => 'suppliesTotal', 'title' => 'Total'],
        'workId' => ['id' => 'inputWorkId', 'name' => 'workId', 'title' => 'ID'],
        'workRef' => ['id' => 'inputWorkRef', 'name' => 'workRef', 'title' => 'Referencia'],
        'workDescription' => ['id' => 'inputWorkDescription', 'name' => 'workDescription', 'title' => 'Descripcion'],
        'workPvp' => ['id' => 'inputWorkPvp', 'name' => 'workPvp', 'title' => 'Precio'],
        'workCantity' => ['id' => 'inputWorkCantity', 'name' => 'workCantity', 'title' => 'Cantidad'],
        'workTotal' => ['id' => 'inputWorkTotal', 'name' => 'workTotal', 'title' => 'Total'],
        'worksPrice' => ['id' => 'inputWorksPrice', 'name' => 'worksPrice', 'title' => 'Importe'],
        'worksTva' => ['id' => 'inputWorksTva', 'name' => 'worksTva', 'title' => 'IVA'],
        'worksTotal' => ['id' => 'inputWorksTotal', 'name' => 'worksTotal', 'title' => 'Total'],
        'dataType' => ['id' => 'inputDataType', 'name' => 'dataType', 'title' => 'Tipo'],
        'variant' => ['id' => 'inputVariant', 'name' => 'variant', 'title' => 'Variante'],
        'version' => ['id' => 'inputVersion', 'name' => 'version', 'title' => 'Version'],
        'comercialName' => ['id' => 'inputComercialName', 'name' => 'comercialName', 'title' => 'Nombre Comercial'],
        'mma' => ['id' => 'inputMma', 'name' => 'mma', 'title' => 'M.M.A'],
        'mmaAxe1' => ['id' => 'inputMmaAxe1', 'name' => 'mmaAxe1', 'title' => 'M.M.A. Eje1'],
        'mmaAxe2' => ['id' => 'inputMmaAxe2', 'name' => 'mmaAxe2', 'title' => 'M.M.A. Eje2'],
        'mmac' => ['id' => 'inputMmac', 'name' => 'mmac', 'title' => 'M.M.A.C.'],
        'mmar' => ['id' => 'inputMmar', 'name' => 'mmar', 'title' => 'M.M.A.R.'],
        'mmarf' => ['id' => 'inputMmarf', 'name' => 'mmarf', 'title' => 'M.M.A.R.F'],
        'mom' => ['id' => 'inputMom', 'name' => 'mom', 'title' => 'M.O.M.'],
        'momAxe1' => ['id' => 'inputMomAxe1', 'name' => 'momAxe1', 'title' => 'M.O.M. Eje1'],
        'momAxe2' => ['id' => 'inputMomAxe2', 'name' => 'momAxe2', 'title' => 'M.O.M. Eje2'],
        'large' => ['id' => 'inputLarge', 'name' => 'large', 'title' => 'Largo'],
        'width' => ['id' => 'inputWidth', 'name' => 'width', 'title' => 'Ancho'],
        'height' => ['id' => 'inputHeight', 'name' => 'height', 'title' => 'Alto'],
        'frontOverhang' => ['id' => 'inputFrontOverhang', 'name' => 'frontOverHang', 'title' => 'Voladizo delantero'],
        'rearOverhang' => ['id' => 'inputRearOverhang', 'name' => 'rearOverhang', 'title' => 'Voladizo trasero'],
        'axeDistance' => ['id' => 'inputAxeDistance', 'name' => 'axeDistance', 'title' => 'Distancia entre ejes'],        
        'chargeLength' => ['id' => 'inputChargeLength', 'name' => 'chargeLength', 'title' => 'Longitud de Carga'],
        'deposit' => ['id' => 'inputDeposit', 'name' => 'deposit', 'title' => 'Deposito'],
        'initCharge' => ['id' => 'inputInitCharge', 'name' => 'initCharge', 'title' => 'Inicio Carga']];
    public function __construct(VehicleService $vehicleService) {
        parent::__construct();
        $this->vehicleService = $vehicleService;       
    }    
    public function getIndexAction() {
        $vehicles = $this->vehicleService->getVehicles();
        return $this->renderHTML('/vehicles/vehiclesList.html.twig', [
            'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
            'list' => $this->list,
            'tab' => $this->tab,
            'title' => $this->title,
            'search' => $this->search,
            'vehicles' => $vehicles
        ]);
    }    
    public function searchVehicleAction($request) {
        $postData = $request->getParsedBody();
        $searchString = $postData['searchFilter'];
        $vehicles = $this->vehicleService->searchVehicles($searchString);        
        return $this->renderHTML('/vehicles/vehiclesList.html.twig',[
            'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
            'list' => $this->list,
            'tab' => $this->tab,
            'title' => $this->title,
            'search' => $this->search,
            'vehicles' => $vehicles            
        ]);
    }    
    public function getVehicleDataAction($request) {                
        $responseMessage = null;        
        if($request->getMethod() == 'POST') {
            $postData = $request->getParsedBody();            
            $vehicleValidator = v::key('plate', v::stringType()->notEmpty());                       
            try{
                $vehicleValidator->assert($postData); // true
                $postData['brand'] = $this->vehicleService->getBrandByName($postData);
                $postData['model'] = $this->vehicleService->getModelByName($postData);
                $postData['type'] = $this->vehicleService->getVehicleTypeByName($postData);
                $responseMessage = $this->vehicleService->saveRegister(new Vehicle(), $postData);
            }catch(Exception $e) {                
                $responseMessage = $e->getMessage();
            }           
        } 
        $brands = $this->vehicleService->getBrands();
        $models = $this->vehicleService->getModels();
        $stores = $this->vehicleService->getStores();
        $locations = $this->vehicleService->getLocations();
        $components = $this->vehicleService->getAllRegisters(new Components());
        $supplies = $this->vehicleService->getAllRegisters(new Supplies());
        $works = $this->vehicleService->getAllRegisters(new Works());
        $accesories = $this->vehicleService->getAllRegisters(new Accesories());
        $types = $this->vehicleService->getTypes();
        $providors = $this->vehicleService->getProvidors();
        $selectedTab = $this->vehicleService->getSelectedTab($request->getQueryParams());
        $vehicleSelected = $this->vehicleService->setVehicle($request->getQueryParams('id')); 
        $selectedAccesories = $this->vehicleService->getVehicleAccesories($vehicleSelected);
        $vehicleComponents = $this->vehicleService->getVehicleComponents($vehicleSelected);
        $vehicleSupplies = $this->vehicleService->getVehicleSupplies($vehicleSelected);
        $selectedComponent = $this->vehicleService->getSelectedComponent($request->getQueryParams());
        $selectedSupply = $this->vehicleService->getSelectedSupply($request->getQueryParams());
        $editPriceComponent = $this->vehicleService->getComponentPrice($request->getQueryParams(), $selectedComponent);
        $editCantityComponent = $this->vehicleService->getComponentCantity($request->getQueryParams());
        $editPriceSupply = $this->vehicleService->getSupplyPrice($request->getQueryParams());
        $editCantitySupply = $this->vehicleService->getSupplyCantity($request->getQueryParams());
        if(isset($request->getQueryParams()['responseMessage'])){
            $responseMessage = $request->getQueryParams()['responseMessage'];
        }       
//        var_dump($brands);die();
        return $this->renderHTML('/vehicles/vehiclesForm.html.twig', [
            'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
            'responseMessage' => $responseMessage,
            'brands' => $brands,
            'models' => $models,
            'stores' => $stores,
            'components' => $components,
            'supplies' => $supplies,
            'works' => $works,
            'locations' => $locations,
            'selectedTab' => $selectedTab,            
            'types' => $types,    
            'providors' => $providors,
            'value' => $vehicleSelected,            
            'accesories' => $accesories,
            'selectedAccesories' => $selectedAccesories,
            'vehicleComponents' => $vehicleComponents,
            'vehicleSupplies' => $vehicleSupplies,
            'selectedComponent' => $selectedComponent,
            'editPriceComponent' => $editPriceComponent,
            'editCantityComponent' => $editCantityComponent,
            'editPriceSupply' => $editPriceSupply,
            'editCantitySupply' => $editCantitySupply,
            'selectedSupply' => $selectedSupply,
            'inputs' => $this->inputs,
            'list' => $this->list,
            'tab' => $this->tab,
            'script' => $this->script,
            'save' => $this->save,
            'search' => $this->search,
            'formName' => $this->formName,
            'title' => $this->title
        ]);
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
