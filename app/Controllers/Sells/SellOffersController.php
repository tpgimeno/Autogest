<?php


namespace App\Controllers\Crm;

use App\Controllers\BaseController;
use App\Models\Accesories;
use App\Models\Brand;
use App\Models\Components;
use App\Models\Customer;
use App\Models\ModelVh;
use App\Models\SellOffer;
use App\Models\SellOffersComponents;
use App\Models\SellOffersSupplies;
use App\Models\SellOffersWorks;
use App\Models\Supplies;
use App\Models\Vehicle;
use App\Models\VehicleTypes;
use App\Models\Works;
use App\Reports\SellOfferReport;
use App\Services\Crm\SellOfferService;
use Illuminate\Database\Capsule\Manager as DB;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Diactoros\ServerRequest;
use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Rules\Date;
use Respect\Validation\Validator as v;
/**
 * Description of SellOffersController
 *
 * @author tonyl
 */
class SellOffersController extends BaseController {    
    protected $sellOfferService;    
    public function __construct(SellOfferService $sellOfferService){
        parent::__construct();
        $this->sellOfferService = $sellOfferService;
    }
//    Funcion que muestra la lista de ofertas
    public function getIndexAction(){
        $offers = DB::table('selloffers')
                ->join('customers', 'selloffers.customer_id', '=', 'customers.id')
                ->join('vehicles', 'selloffers.vehicle_id', '=', 'vehicles.id')
                ->join('brands', 'vehicles.brand', '=', 'brands.id')
                ->join('models', 'vehicles.model', '=', 'models.id')
                ->select('selloffers.id', 'selloffers.offer_number', 'selloffers.offer_date', 'customers.name as name', 'brands.name as brand', 'models.name as model')
                ->whereNull('selloffers.deleted_at')
                ->get();        
        return $this->renderHTML('/sells/offers/sellOffersList.html.twig', [
            'currentUser' => $this->currentUser->getCurrentUserEmailAction(),
            'offers' => $offers
        ]);
    }
    public function searchSellOffersAction($request){
        $postData = $request->getParsedBody();
        $searchString = $postData['searchFilter'];
        $offers = DB::table('selloffers')
                ->join('customers', 'selloffers.customer_id', '=', 'customers.id')
                ->join('vehicles', 'selloffers.vehicle_id', '=', 'vehicles.id')
                ->join('brands', 'vehicles.brand', '=', 'brands.id')
                ->join('models', 'vehicles.model', '=', 'models.id')
                ->select('selloffers.offer_number', 'customers.name as customer_name', 'brands.name as brand',
                        'models.name as model')
                ->where('selloffers.offer_date', 'like', '%'.$searchString.'%')
                ->orWhere('selloffers.offer_number', 'like', '%'.$searchString.'%')
                ->orWhere('customers.name', 'like', '%'.$searchString.'%')
                ->orWhere('brands.name', 'like', '%'.$searchString.'%')
                ->orWhere('models.name', 'like', '%'.$searchString.'%')
                ->whereNull('deleted_at')
                ->get();        
        return $this->renderHTML('/sells/offers/sellOffersList.html.twig', [
            'currentUser' => $this->currentUser->getCurrentUserEmailAction(),
            'offers' => $offers
        ]);
    }
    public function getSellOffersDataAction($request){
        $responseMessage = null;
        if($request->getMethod() === 'POST')
        {
            $postData = $request->getParsedBody();            
            $offerValidator = v::key('offer_number', v::stringType()->notEmpty())
                    ->key('customer_id', v::notEmpty())
                    ->key('vehicle_id', v::notEmpty());
            try
            {
                 $offerValidator->assert($postData);
                 $offer = new SellOffer();
                 $this->saveSellOffersDataAction($offer, $postData);                             
            } 
            catch (Exception $ex) 
            {
                $responseMessage = $ex->getMessage();
            }
        }                        
        $params = $request->getQueryParams();
        $selected_tab = 'offer';                
        return $this->getRenderAgain($params, $selected_tab, $responseMessage);
    }   
    public function saveSellOffersDataAction($offer, $postData){        
        $offer->id = $postData['id'];                 
        $offer_selected = false;
        if(SellOffer::find($offer->id)->first())
        {                     
           $offer = SellOffer::find($offer->id);
           $offer_selected = true;                                         
        }
        $this->getPostDataSellOffer($offer);                
        if($offer_selected === true)
        {
            $offer->update();
            $responseMessage = 'Updated';
        }
        else
        {
            $offer->save();
            $responseMessage = 'Saved';
        }
    }
    public function getPostDataSellOffer($offer){
        $offer->offer_number = $postData['offer_number'];
        $time = strtotime($postData['date']);
        $offer->offer_date = Date('y/m/d', $time);
        $offer->texts = $postData['texts'];
        $offer->observations = $postData['observations'];                 
        $customer = Customer::find($postData['customer_id']);                 
        $offer->customer_id = $customer->id;
        $vehicle = Vehicle::find($postData['vehicle_id']);
        $offer->vehicle_id = $vehicle->id;                 
        $offer->discount = $this->tofloat($postData['discount']);                 
        $offer->pvp = $this->tofloat($postData['price']);
        $offer->tva = $this->tofloat($postData['tva']);
        $offer->total = $this->tofloat($postData['total']);
        $offer->vehicle_pvp = $this->tofloat($postData['price_vehicle']) - $this->tofloat($postData['vehicle_discount']);                
        $offer->vehicle_tva = $this->tofloat($postData['vehicle_tva']);
        $offer->vehicle_total = $this->tofloat($postData['vehicle_total']);
        $offer->vehicle_comments = $postData['vehicle_comments'];
        return $offer;
    }
    public function deleteAction(ServerRequest $request){
        $params = $request->getQueryParams();       
        $this->sellOfferService->deleteOffer(intval($params['offer_id']));
        return new RedirectResponse('/intranet/crm/offers/list');
    }
    public function searchCustomerSellOfferAction(ServerRequestInterface $request)
    {
        $customers = null;      
        $postData = $request->getParsedBody();       
        $searchString = $postData['searchCustomerFilter'];
        if($searchString == "")
        {
            $customers = Customer::All();
        }
        else
        {
          $customers= Customer::Where("name", "like", "%".$searchString."%" )
            ->orWhere("fiscal_id", "like", "%".$searchString."%")
            ->orWhere("address", "like", "%".$searchString."%")
            ->orWhere("phone", "like", "%".$searchString."%")
            ->orWhere("email", "like", "%".$searchString."%")
            ->get();  
        } 
        $response = new JsonResponse($customers);        
        return $response;                  
    }
    public function selectCustomerSellOfferAction($request) {
        $responseMessage = null;
        $selected_tab = 'customer';
        $params = $request->getQueryParams();
        return $this->getRenderAgain($params, $selected_tab, $responseMessage);        
    }
    public function searchVehicleSellOfferAction($request)
    {
        $vehicles = null;      
        $postData = $request->getParsedBody();       
        $searchString = $postData['searchVehicleFilter'];
        if($searchString == "")
        {
            $vehicles = Vehicle::All();
        }
        else
        {
            $vehicles= DB::table('vehicles')
                ->join('brands', 'vehicles.brand', '=', 'brands.id')
                ->join('models', 'vehicles.model', '=', 'models.id')
                ->join('vehicle_types', 'vehicles.type', '=', 'vehicle_types.id')
                ->select('vehicles.id', 'vehicles.plate', 'vehicles.vin', 
                        'vehicles.description as description', 'vehicles.location', 'vehicle_types.name as type', 
                        'brands.name as brand', 'models.name as model', 'vehicles.color', 'vehicles.places',
                        'vehicles.doors', 'vehicles.power', 'vehicles.cost', 'vehicles.pvp', 'vehicles.accesories')
                ->where("brands.name", "like", "%".$searchString."%" )
                ->orWhere("models.name", "like", "%".$searchString."%")
                ->orWhere("vehicles.description", "like", "%".$searchString."%")
                ->orWhere("vehicles.plate", "like", "%".$searchString."%")
                ->orWhere("vehicles.vin", "like", "%".$searchString."%")
                ->orWhere("vehicle_types.name", "like", "%".$searchString."%")
                ->orWhere("vehicles.id", "like", "%".$searchString."%")
                ->WhereNull('vehicles.deleted_at')
                ->get();         
        }                 
        $response = new JsonResponse($vehicles);        
        return $response;
    }
    public function selectVehicleSellOfferAction($request) {
        $responseMessage = null;
        $params = $request->getQueryParams();        
        $selected_tab = 'vehicle';
        return $this->getRenderAgain($params, $selected_tab, $responseMessage);       
    }    
    public function searchSuppliesSellOffersAction($request) {
        $postData = $request->getParsedBody();
        $searchString = $postData['searchFilter'];
        if($searchString == null)
        {
            $supplies = Supplies::All();
        }
        else
        {
            $supplies = DB::table('supplies')
                ->join('maders', 'supplies.mader', '=', 'maders.id')
                ->select('supplies.id', 'supplies.ref', 'maders.name', 'supplies.pvp')
                ->where('supplies.ref', 'like', "%".$searchString."%")
                ->orWhere('supplies.name', 'like', "%".$searchString."%")
                ->orWhere('maders.name', 'like', "%".$searchString."%")
                ->orWhere('supplies.mader_code', 'like', "%".$searchString."%")
                ->whereNull('deleted_at')
                ->get();
        }
        $response = new JsonResponse($supplies);        
        return $response;
    }
    public function selectSuppliesSellOffersAction($request) {               
        $responseMessage = null;
        $params = $request->getQueryParams();
        $selected_tab = 'supplies';
        return $this->getRenderAgain($params, $selected_tab, $responseMessage);        
    }
    public function addSuppliesSellOffersAction($request) {
        $responseMessage = null;
        $postData = $request->getParsedBody();
        $data = json_decode($postData['supply']);        
        $supply = new SellOffersSupplies();
        $temp_supply = SellOffersSupplies::where('supply_id', '=', $data->supply_id)
                ->where('selloffer_id', '=', $data->selloffer_id)
                ->first();
        if($temp_supply)
        {
            $supply = $temp_supply;
            $supply->cantity += $data->cantity;
        }  
        else
        {
            $supply->cantity = $data->cantity;
        }
        $supply->supply_id = $data->supply_id;
        $supply->selloffer_id = $data->selloffer_id;        
        $supply->price = $data->price;        
        if($temp_supply)
        {
            $supply->update();
            $responseMessage = 'Supply Updated';
        }
        else
        {
            $supply->save();
            $responseMessage = 'Supply Saved';
        }
        $response = new JsonResponse($responseMessage);
        return $response;       
    }
    public function editSuppliesSellOffersAction($request) {
        $responseMessage = 'Editando Recambio';
        $selected_tab = 'supplies';
        $params = $request->getQueryParams();  
        
        $supply = DB::table('selloffers_supplies')                
                ->join('supplies', 'selloffers_supplies.supply_id', '=', 'supplies.id')
                ->join('maders', 'supplies.mader', '=', 'maders.id')
                ->select('selloffers_supplies.id', 'selloffers_supplies.selloffer_id', 'selloffers_supplies.supply_id', 'supplies.ref as reference', 'maders.name as mader', 'supplies.name as name', 'selloffers_supplies.cantity as cantity', 'selloffers_supplies.price as price')
                ->where('selloffers_supplies.selloffer_id', '=', $params['offer_id'])
                ->where('selloffers_supplies.supply_id', '=', $params['supply_id'])
                ->first();//        
        if($supply !== null)
        {
            $array = (['supply_price' => $supply->price ,'supply_cantity' => $supply->cantity]);
            $params = array_merge($params, $array);
            $editSupply = SellOffersSupplies::find($supply->id)->first(); 
            if($editSupply)
            {
                $editSupply->delete();
            } 
        }               
        return $this->getRenderAgain($params, $selected_tab, $responseMessage);        
    }
    public function delSuppliesSellOffersAction($request)  {
        $responseMessage = 'Recambio eliminado';
        $selected_tab = 'supplies';
        $params = $request->getQueryParams();        
        $supply = SellOffersSupplies::where('supply_id', '=', $params['supply_id'])
                ->where('selloffer_id', '=', $params['offer_id'])
                ->first();
        if($supply)
        {
            $supply->delete();
        }
        return $this->getRenderAgain($params, $selected_tab, $responseMessage);
    }
    public function searchComponentsSellOffersAction($request) {        
        $searchString = null;
        $postData = $request->getParsedBody();
        if(isset($postData['searchFilter'])){
            $searchString = $postData['searchFilter'];
        }        
        if($searchString == null)
        {
            $components = Components::All();
        }
        else
        {
            $components = DB::table('components')
                 ->join('maders', 'components.mader', '=', 'maders.id')
                 ->select('components.id', 'components.ref', 'components.serial_number', 'components.pvp')
                 ->where('components.id', 'like', "%".$searchString."$")
                 ->orWhere('components.ref', 'like', "$".$searchString."$")
                 ->orWhere('maders.name', 'like', "$".$searchString."$")
                 ->orWhere('components.serial_number', 'like', "$".$searchString."$")
                 ->whereNull('deleted_at')
                 ->get();
        }
        $response = new JsonResponse($components);        
        return $response;
    }    
    public function selectComponentsSellOffersAction($request)
    {             
        $responseMessage = null;
        $params = $request->getQueryParams();
        $selected_tab = 'supplies';
        return $this->getRenderAgain($params, $selected_tab, $responseMessage);        
    }
    public function addComponentsSellOffersAction($request)
    {
        $responseMessage = null;
        $postData = $request->getParsedBody();
        $data = json_decode($postData['component']);        
        $component = new SellOffersComponents();
        $temp_component = SellOffersComponents::where('component_id', '=', $data->component_id)
                ->where('selloffer_id', '=', $data->selloffer_id)
                ->first();
        if($temp_component)
        {
            $component = $temp_component;
        }        
        $component->component_id = $data->component_id;
        $component->selloffer_id = $data->selloffer_id;
        $component->cantity = $data->cantity;
        $component->price = $data->price;
        if($temp_component)
        {
            $component->update();
            $responseMessage = 'Component Updated';
        }
        else
        {
            $component->save();
            $responseMessage = 'Component Saved';
        }
        $response = new JsonResponse($responseMessage);
        return $response;        
    }
    public function editComponentsSellOffersAction($request) {
        $responseMessage = 'Editando Component';
        $selected_tab = 'supplies';
        $params = $request->getQueryParams();       
        $component = DB::table('selloffers_components')                
                ->join('components', 'selloffers_components.component_id', '=', 'components.id')
                ->join('maders', 'components.mader', '=', 'maders.id')
                ->select('selloffers_components.id', 'selloffers_components.selloffer_id', 'selloffers_components.component_id', 'components.ref as reference', 'maders.name as mader', 'components.name as name', 'selloffers_components.cantity as cantity', 'selloffers_components.price as price')
                ->where('selloffers_components.selloffer_id', '=', $params['offer_id'])
                ->where('selloffers_components.component_id', '=', $params['component_id'])
                ->first();    
        if($component)
        {
            $array = (['component_price' => $component->price ,'component_cantity' => $component->cantity]);
            $params = array_merge($params, $array);
            $editComponent = SellOffersComponents::find($component->id)->first();            
            if($editComponent)
            {
                $editComponent->delete();
            } 
        }           
        return $this->getRenderAgain($params, $selected_tab, $responseMessage);        
    }
    public function delComponentsSellOffersAction($request)
    {
        $responseMessage = 'Componente eliminado';
        $selected_tab = 'supplies';
        $params = $request->getQueryParams(); 
        
        $component = SellOffersComponents::where('component_id', '=', $params['component_id'])
                ->where('selloffer_id', '=', $params['offer_id'])
                ->first();
        if($component)
        {
            $component->delete();
        }
        return $this->getRenderAgain($params, $selected_tab, $responseMessage);
    }
    public function selectWorksSellOffersAction($request)
    {
        $responseMessage = null;
        $params = $request->getQueryParams();
        
        $selected_tab = 'works';
        return $this->getRenderAgain($params, $selected_tab, $responseMessage);
    }    
     public function searchWorksSellOffersAction($request)
    {        
        $searchString = null;
        $postData = $request->getParsedBody();
        if(isset($postData['searchFilter'])){
            $searchString = $postData['searchFilter'];
        }        
        if($searchString == null)
        {
            $works = Components::All();
        }
        else
        {
            $works = DB::table('works')                 
                 ->select('works.id', 'works.reference', 'works.description', 'works.pvp')
                 ->where('works.id', 'like', "%".$searchString."$")
                 ->orWhere('works.description', 'like', "$".$searchString."$")                
                 ->orWhere('works.reference', 'like', "$".$searchString."$")
                 ->whereNull('deleted_at')
                 ->get();
        }
        $response = new JsonResponse($works);        
        return $response;
    }
    public function addWorksSellOffersAction($request)
    {
        $responseMessage = null;
        $postData = $request->getParsedBody();
        $data = json_decode($postData['work']);        
        $work = new SellOffersWorks();
        $temp_work = SellOffersWorks::where('work_id', '=', $data->work_id)
                ->where('selloffer_id', '=', $data->selloffer_id)
                ->first();
        if($temp_work)
        {
            $work = $temp_work;
        }        
        $work->work_id = $data->work_id;
        $work->selloffer_id = $data->selloffer_id;
        $work->cantity = $data->cantity;
        $work->price = $data->price;
        if($temp_work)
        {
            $work->update();
            $responseMessage = 'Work Updated';
        }
        else
        {
            $work->save();
            $responseMessage = 'Work Saved';
        }
        $response = new JsonResponse($responseMessage);
        return $response;       
        
    }  
    public function editWorksSellOffersAction($request)
    {
        $responseMessage = 'Editando Trabajo';
        $selected_tab = 'works';
        $params = $request->getQueryParams();        
        $work = DB::table('selloffers_works')                
                ->join('works', 'selloffers_works.work_id', '=', 'works.id')                
                ->select('selloffers_works.id', 'selloffers_works.selloffer_id', 'selloffers_works.work_id', 'works.reference', 'works.description', 'selloffers_works.cantity', 'selloffers_works.price')
                ->where('selloffers_works.selloffer_id', '=', $params['offer_id'])
                ->where('selloffers_works.work_id', '=', $params['work_id'])
                ->first();         
        if($work)
        {
            $array = (['work_price' => $work->price ,'work_cantity' => $work->cantity]);
            $params = array_merge($params, $array);
            $editWork = SellOffersWorks::find($work->id)->first(); 
            if($editWork)
            {
                $editWork->delete();
            } 
        }           
        return $this->getRenderAgain($params, $selected_tab, $responseMessage);        
    }
    public function delWorksSellOffersAction($request)
    {
        $responseMessage = 'Trabajo eliminado';
        $selected_tab = 'works';
        $params = $request->getQueryParams();        
        $work = SellOffersWorks::where('work_id', '=', $params['work_id'])
                ->where('selloffer_id', '=', $params['offer_id'])
                ->first();
        if($work)
        {
            $work->delete();
        }
        return $this->getRenderAgain($params, $selected_tab, $responseMessage);
    }
    public function getReportAction($request)
    {        
        $postData = $request->getParsedBody();
        $selected_accesories = $this->getSellOfferVehicleAccesories($postData['vehicle_id'])->toArray();        
        $offerSupplies = $this->getSellOfferSupplies($postData['id'])->toArray();
        $offerComponents = $this->getSellOfferComponents($postData['id'])->toArray();
        $offerWorks = $this->getSellOfferWorks($postData['id'])->toArray();
        array_push($postData, $offerSupplies, $offerComponents, $offerWorks, $selected_accesories);
        $report = new SellOfferReport();
        $report->AddPage();
        $report->Body($postData);        
        $report->Output();
    }
    public function getRenderAgain($params, $selected_tab, $responseMessage)
    {        
        $selected_offer = null;
        $last_offer = null;
        $new_offer= null;
        $offer_number = null;               
        if(isset($params['offer_id'])&& $params['offer_id'])
        {           
            $selected_offer = SellOffer::find($params['offer_id']);            
        }        
        if($selected_offer === null)
        {
            $last_offer = DB::table('selloffers')
                    ->get()->last();            
            if($last_offer === null)
            {
                $new_offer = 1;
                $offer_number = $this->getFirstSellOfferNumber();                                  
            }
            else
            {
                $new_offer = $last_offer->id + 1;
                $offer_number = $this->getNewSellOfferNumber($last_offer);                             
            }
            if($selected_tab === 'offer')
            {
                $this->resetOffer($new_offer);
            }   
        }
        else
        {
            $new_offer = $selected_offer->id;
            $offer_number = $selected_offer->offer_number;
        }        
        $selected_supply = null;  
        $editPriceSupply = null;
        $editCantitySupply = null;        
        if(isset($params['supply_id']) && $params['supply_id'])
        {                
            if($params['supply_id'] !== 'null')
            {                    
                $selected_supply = Supplies::find($params['supply_id']);
            }            
            if(isset($params['supply_price']) && $params['supply_price'] !== 'null')
            {               
                $editPriceSupply = $params['supply_price'];                
            }
            else if($selected_supply)
            {               
                $editPriceSupply = $selected_supply->pvp;                                
            }
            if(isset($params['supply_cantity']) && $params['supply_cantity'] !== 'null')
            {               
                $editCantitySupply = $params['supply_cantity'];                
            }            
        } 
        $selected_component = null;
        $editPriceComponent = null;
        $editCantityComponent = null;
        if(isset($params['component_id']) && $params['component_id'] !== 'null')
        {
            $selected_component = Components::find($params['component_id']);                      
            if(isset($params['component_price']) && $params['component_price'] !== 'null')
            {               
                $editPriceComponent = $params['component_price'];                
            }
            else if($selected_component)
            {               
                $editPriceComponent = $selected_component->pvp;                
            }
            if(isset($params['component_cantity']) && $params['component_cantity'] !== 'null')
            {            
               $editCantityComponent = $params['component_cantity'];                
            }
        }
        $selected_work = null;        
        $editPriceWork = null;
        $editCantityWork = null;
        if(isset($params['work_id']) && $params['work_id'] !== 'null')
        {           
            $selected_work = Works::find($params['work_id']);                       
            if(isset($params['work_price']) && $params['work_price'] !== 'null')
            {                
                $editPriceWork = $params['work_price'];                
            }
            else if($selected_work !== null)
            {             
                $editPriceWork = $selected_work->price;               
            }
            if(isset($params['work_cantity']) && $params['work_cantity'] !== 'null')
            {
                $editCantityWork = $params['work_cantity'];             
            }
        }
        $selected_customer = null;
        if(isset($params['customer_id']) && $params['customer_id'])
        { 
            $selected_customer = Customer::find($params['customer_id']);            
        }
        $selected_vehicle = null;
        $brand = null;
        $model = null;
        $selected_accesories = null;
        if(isset($params['vehicle_id']) && $params['vehicle_id'])
        {
                $selected_vehicle = Vehicle::find($params['vehicle_id']);
                $brand = Brand::find($selected_vehicle->brand);
                $model = ModelVh::find($selected_vehicle->model); 
                $selected_accesories = $this->getSellOfferVehicleAccesories($params['vehicle_id'])->toArray();
            
        }               
        if($selected_offer)
        {
            $selected_vehicle = Vehicle::find($selected_offer->vehicle_id);
            $brand = Brand::find($selected_vehicle->brand);
            $model = ModelVh::find($selected_vehicle->model);
            $selected_customer = Customer::find($selected_offer->customer_id);
            $selected_accesories = $this->getSellOfferVehicleAccesories($selected_offer->vehicle_id)->toArray();            
        }      
        try
        {            
            $offerSupplies = $this->getSellOfferSupplies($new_offer);
            $offerComponents = $this->getSellOfferComponents($new_offer);
            $offerWorks = $this->getSellOfferWorks($new_offer);         
            $types = VehicleTypes::All();
            $supplies = Supplies::All();
            $components = Components::All();
            $works = Works::All();
            $customers = Customer::All();
            $vehicles = Vehicle::All();
            $accesories = Accesories::All();
            return $this->renderHTML('/sells/offers/sellOffersForm.html.twig', [
                'sellOffer' => $selected_offer,
                'offer_number' => $offer_number,
                'customers' => $customers,
                'selected_customer' => $selected_customer,
                'vehicles' => $vehicles,
                'selected_vehicle' => $selected_vehicle,
                'brand' => $brand,
                'model' => $model,
                'types' => $types,
                'accesories' => $accesories,
                'selected_accesories' => $selected_accesories,
                'supplies' => $supplies,
                'selected_supply' => $selected_supply,
                'offerSupplies' => $offerSupplies,
                'edit_price_supply' => $editPriceSupply,
                'edit_cantity_supply' => $editCantitySupply,
                'selected_component' => $selected_component,
                'components' => $components, 
                'offerComponents' => $offerComponents,
                'edit_price_component' => $editPriceComponent,
                'edit_cantity_component' => $editCantityComponent,
                'selected_work' => $selected_work,
                'selected_tab' => $selected_tab,                
                'works' => $works,                
                'offerWorks' => $offerWorks,
                'edit_price_work' => $editPriceWork,
                'edit_cantity_work' => $editCantityWork,
                'new_selloffer' => $new_offer,                
                'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
                'responseMessage' => $responseMessage
            ]); 
        } catch (Exception $ex) 
        {
              $responseMessage = $ex->getMessage();         
        }
    }
    public function getFirstSellOfferNumber() {
        
        $stringNumber = null;
        $offerString = '2020OF';        
        $offer_integer = 1;
        $offerStringNumber = strval($offer_integer);                
        while(strlen($offerStringNumber) < 4)
        {
            $offerStringNumber = "0".$stringNumber;                    
        }
        $offer_number = $offerString.$offerStringNumber;
        return $offer_number;         
    }
    public function getNewSellOfferNumber($last_offer){  
        $offerString = '2020OF';
        $offer_string = substr($last_offer->offer_number, 6, 4);
        $offer_integer = intval($offer_string) + 1;
        $stringNumber = strval($offer_integer);                
        while(strlen($stringNumber) < 4)
        {
            $stringNumber = "0".$stringNumber;
        }
        return $offerString.$stringNumber;
    }
    public function resetOffer($offer_id)
    {
        if(!SellOffer::find($offer_id))
        {
            $this->cleanSupplies($offer_id);
            $this->cleanComponents($offer_id);
            $this->cleanWorks($offer_id);              
        }        
    }
    public function getSellOfferSupplies($offer_id)
    {
        $offerSupplies = DB::table('selloffers_supplies')                
            ->join('supplies', 'selloffers_supplies.supply_id', '=', 'supplies.id')
            ->join('maders', 'supplies.mader', '=', 'maders.id')
            ->select('selloffers_supplies.id', 'selloffers_supplies.selloffer_id', 'selloffers_supplies.supply_id', 'supplies.ref as reference', 'maders.name as mader', 'supplies.name as name', 'selloffers_supplies.cantity as cantity', 'selloffers_supplies.price as price')
            ->where('selloffers_supplies.selloffer_id', '=', $offer_id)
            ->get();
        return $offerSupplies;
    }
    public function cleanSupplies($offer_id)
    {
        $offerSupplies = $this->getSellOfferSupplies($offer_id);         
        for($i = 0; $i < count($offerSupplies); $i++)
        {
            $offerSupply = SellOffersSupplies::find($offerSupplies[$i]->id);                
            if($offerSupply)
            {
                $offerSupply->delete();
            }                
        }
    }
    public function getSellOfferComponents($offer_id)
    {
        $offerComponents = DB::table('selloffers_components')
            ->join('components', 'selloffers_components.component_id', '=', 'components.id')
            ->join('maders', 'components.mader', '=', 'maders.id')
            ->select('selloffers_components.id', 'selloffers_components.selloffer_id', 'selloffers_components.component_id', 'components.ref as reference', 'components.name as name', 'selloffers_components.cantity as cantity', 'selloffers_components.price as price')
            ->where('selloffers_components.selloffer_id', '=', $offer_id)                
            ->get();
        return $offerComponents;
    }
    public function cleanComponents($offer_id) {
        $offerComponents = $this->getSellOfferComponents($offer_id);            
        for($i = 0; $i < count($offerComponents); $i++)
        {                
            $offerComponent = SellOffersComponents::find($offerComponents[$i]->id);                
            if($offerComponent)
            {
                $offerComponent->delete();
            }                
        }
    }
    public function getSellOfferWorks($offer_id){
        $offerWorks = DB::table('selloffers_works')
            ->join('works', 'selloffers_works.work_id', '=', 'works.id')               
            ->select('selloffers_works.id', 'selloffers_works.selloffer_id', 'selloffers_works.work_id', 'works.description as description', 'selloffers_works.cantity as cantity', 'selloffers_works.price as price')
            ->where('selloffers_works.selloffer_id', '=', $offer_id)                
            ->get();
        return $offerWorks;
    }            
    public function cleanWorks($offer_id) {
        $offerWorks = $this->getSellOfferWorks($offer_id);
        for($i = 0; $i < count($offerWorks); $i++)
        {
            $offerWork = SellOffersWorks::find($offerWorks[$i]->id);
            if($offerWork)
            {
                $offerWork->delete();
            }                
        }
    } 
    public function getSellOfferVehicleAccesories($vehicle_id)
    {
        $selected_accesories = DB::table('vehicle_accesories')
                ->join('accesories', 'vehicle_accesories.accesory_id', '=', 'accesories.id')
                ->select('vehicle_accesories.accesory_id','vehicle_accesories.id', 'accesories.keystring', 'accesories.name')
                ->where('vehicle_accesories.vehicle_id', '=', $vehicle_id)
                ->get();
        return $selected_accesories;
    }
}
