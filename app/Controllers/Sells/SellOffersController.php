<?php


namespace App\Controllers\Sells;

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
use App\Models\VehicleAccesories;
use App\Models\VehicleTypes;
use App\Models\Works;
use App\Reports\Sells\SellOfferDetailedReport;
use App\Reports\Sells\SellOfferVehicleReport;
use App\Services\Sells\SellOfferService;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\QueryException;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Diactoros\ServerRequest;
use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Rules\Date;
use Respect\Validation\Validator as v;
use ZipStream\Exception;
use function GuzzleHttp\json_decode;
use function PHPUnit\Framework\isEmpty;
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
                ->join('customers', 'selloffers.customerId', '=', 'customers.id')
                ->join('vehicles', 'selloffers.vehicleId', '=', 'vehicles.id')
                ->join('brands', 'vehicles.brand', '=', 'brands.id')
                ->join('models', 'vehicles.model', '=', 'models.id')
                ->select('selloffers.id', 'selloffers.offerNumber', 'selloffers.offerDate', 'customers.name as name', 'brands.name as brand', 'models.name as model')
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
                ->join('customers', 'selloffers.customerId', '=', 'customers.id')
                ->join('vehicles', 'selloffers.vehicleId', '=', 'vehicles.id')
                ->join('brands', 'vehicles.brand', '=', 'brands.id')
                ->join('models', 'vehicles.model', '=', 'models.id')
                ->select('selloffers.offerNumber', 'customers.name as customerName', 'brands.name as brand',
                        'models.name as model')
                ->where('selloffers.offerDate', 'like', '%'.$searchString.'%')
                ->orWhere('selloffers.offerNumber', 'like', '%'.$searchString.'%')
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
    public function getSellOffersDataAction($request)
    {
        $responseMessage = null;
        $offer = null;
        if($request->getMethod() == 'POST')
        {
            $postData = $request->getParsedBody();            
            if($this->validateData($postData))
            {
                $responseMessage = $this->validateData($postData);
            }
            $offer = $this->getPostDataSellOffer($postData);
            $responseMessage = $this->saveSellOffersDataAction($offer);            
        }                        
        $params = $request->getQueryParams();
        if(isEmpty($params) && $offer)
        {
            $params = array('offer_id' => $offer->id);
        }
        
        $selected_tab = 'offer'; 
        $selected_offer = $this->getSelectedOfferData($params);        
        $new_offer = null;
        $offer_number = null;        
        if($selected_offer === null)
        {
            $last_offer = DB::table('selloffers')->get()->last();            
            if($last_offer === null)
            {
                $new_offer = 1;                
                $offer_number = $this->getFirstSellOfferNumber(); 
                $this->resetOffer($new_offer);
            }
            else
            {
                $new_offer = $last_offer->id + 1;
                $offer_number = $this->getNewSellOfferNumber($last_offer); 
                $this->resetOffer($new_offer);
            }              
        }
        else
        {
            $new_offer = $selected_offer->id;
            $offer_number = $selected_offer->offerNumber;
        } 
                       
        return $this->getRenderAgain($params, $new_offer, $offer_number, $selected_tab, $responseMessage);
    }  
    public function validateData($postData)
    {
        $responseMessage = null;
        $offerValidator = v::key('offer_number', v::stringType()->notEmpty())
                    ->key('customer_id', v::notEmpty())
                    ->key('vehicle_id', v::notEmpty());
        try
        {
             $offerValidator->assert($postData);

        } 
        catch (Exception $ex) 
        {
            $responseMessage = $ex->getMessage();
        }
        return $responseMessage;
    }
    public function saveSellOffersDataAction($offer)
    {               
        try{
            if(SellOffer::find($offer->id))
            {                     
                $offer->update();
                $responseMessage = 'Updated';                                      
            }
            else
            {
                $offer->save();
                $responseMessage = 'Saved';
            }
        } catch (QueryException $ex) {
            $responseMessage = $this->errorService->getError($ex);
        }  
        return $responseMessage;
    }
    public function getPostDataSellOffer($postData)
    {
        $offer = new SellOffer();
        
        if(isset($postData['id']) && $postData['id'])
        {
            if(SellOffer::find($postData['id']))
            {
                $offer = SellOffer::find($postData['id'])->first();
            }            
        }        
        $offer->offerNumber = $postData['offer_number'];
        $offer->offerDate = Date('y/m/d', strtotime($postData['date']));
        $offer->texts = $postData['texts'];
        $offer->observations = $postData['observations'];                       
        $offer->customerId = Customer::find($postData['customer_id'])->id;
        $offer->vehicleId = Vehicle::find($postData['vehicle_id'])->id;                 
        $offer->discount = $this->tofloat($postData['discount']);                 
        $offer->pvp = $this->tofloat($postData['price']);
        $offer->tva = $this->tofloat($postData['tva']);
        $offer->total = $this->tofloat($postData['total']);
        $offer->vehiclePvp = $this->tofloat($postData['price_vehicle']) - $this->tofloat($postData['vehicle_discount']);                
        $offer->vehicleTva = $this->tofloat($postData['vehicle_tva']);
        $offer->vehicleTotal = $this->tofloat($postData['vehicle_total']);
        $offer->equipment = nl2br($postData['equipment']);
        $offer->vehicleComments = nl2br($postData['vehicle_comments']);
        return $offer;
    }
    public function deleteAction(ServerRequest $request){
        $params = $request->getQueryParams();       
        $this->sellOfferService->deleteOffer(intval($params['offer_id']));
        return new RedirectResponse('/Intranet/crm/offers/list');
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
            ->orWhere("fiscalId", "like", "%".$searchString."%")
            ->orWhere("address", "like", "%".$searchString."%")
            ->orWhere("phone", "like", "%".$searchString."%")
            ->orWhere("email", "like", "%".$searchString."%")
            ->get();  
        } 
        $response = new JsonResponse($customers);        
        return $response;                  
    }
    public function selectCustomerSellOfferAction($request) 
    {
        $responseMessage = null;
        $selected_tab = 'customer';
        $params = $request->getQueryParams();        
        return $this->getRenderAgain($params, null, null, $selected_tab, $responseMessage);        
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
                ->join('vehicletypes', 'vehicles.type', '=', 'vehicletypes.id')
                ->select('vehicles.id', 'vehicles.plate', 'vehicles.vin', 
                        'vehicles.description as description', 'vehicles.location', 'vehicleTypes.name as type', 
                        'brands.name as brand', 'models.name as model', 'vehicles.color', 'vehicles.places',
                        'vehicles.doors', 'vehicles.power', 'vehicles.cost', 'vehicles.pvp', 'vehicles.accesories')
                ->where("brands.name", "like", "%".$searchString."%" )
                ->orWhere("models.name", "like", "%".$searchString."%")
                ->orWhere("vehicles.description", "like", "%".$searchString."%")
                ->orWhere("vehicles.plate", "like", "%".$searchString."%")
                ->orWhere("vehicles.vin", "like", "%".$searchString."%")
                ->orWhere("vehicletypes.name", "like", "%".$searchString."%")
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
        return $this->getRenderAgain($params, null, null, $selected_tab, $responseMessage);       
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
                ->orWhere('supplies.maderCode', 'like', "%".$searchString."%")
                ->whereNull('deleted_at')
                ->get();
        }
        $response = new JsonResponse($supplies);        
        return $response;
    }
    public function selectSuppliesSellOffersAction($request) 
    {               
        $responseMessage = null;
        $params = $request->getQueryParams();
        $selected_tab = 'supplies'; 
        return $this->getRenderAgain($params, null, null, $selected_tab, $responseMessage);        
    }
    public function addSuppliesSellOffersAction($request) 
    {
        $responseMessage = null;
        $postData = $request->getParsedBody();        
        $data = json_decode($postData['supply']);        
        $supply = new SellOffersSupplies();        
        $temp_supply = SellOffersSupplies::where('supplyId', '=', $data->supplyId)
                ->where('sellofferId', '=', $data->sellofferId);
        if($temp_supply)
        {
            $supply = $temp_supply;
            $supply->cantity += $data->cantity;
        }  
        else
        {
            $supply->cantity = $data->cantity;
        }
        $supply->supplyId = $data->supplyId;
        $supply->sellofferId = $data->sellofferId;        
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
    public function editSuppliesSellOffersAction($request) 
    {
        $responseMessage = 'Editando Recambio';
        $selected_tab = 'supplies';
        $params = $request->getQueryParams();         
        $supply = DB::table('sellofferssupplies')                
                ->join('supplies', 'sellofferssupplies.supplyId', '=', 'supplies.id')
                ->join('maders', 'supplies.mader', '=', 'maders.id')
                ->select('sellofferssupplies.id', 'sellofferssupplies.sellofferId', 'sellofferssupplies.supplyId', 'supplies.ref as reference', 'maders.name as mader', 'supplies.name as name', 'sellofferssupplies.cantity as cantity', 'sellofferssupplies.price as price')
                ->where('sellofferssupplies.sellofferId', '=', $params['offer_id'])
                ->where('sellofferssupplies.supplyId', '=', $params['supply_id'])
                ->first(); 
        
        if($supply !== null)
        {
            $array = (['supply_ref' => $supply->reference, 'supply_name' => $supply->name,  'supply_price' => $supply->price ,'supply_cantity' => $supply->cantity]);
            $params = array_merge($params, $array);
            $editSupply = SellOffersSupplies::find($supply->id); 
            if($editSupply)
            {
                $editSupply->delete();
            } 
        }       
        return $this->getRenderAgain($params, null, null, $selected_tab, $responseMessage);        
    }
    public function delSuppliesSellOffersAction($request)  {
        $responseMessage = 'Recambio eliminado';
        $selected_tab = 'supplies';
        $params = $request->getQueryParams();        
        $supply = SellOffersSupplies::where('supplyId', '=', $params['supply_id'])
                ->where('sellofferId', '=', $params['offer_id'])
                ->first();
        if($supply)
        {
            $supply->delete();
        }
        return $this->getRenderAgain($params, null, null, $selected_tab, $responseMessage);
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
    public function selectComponentsSellOffersAction($request)
    {             
        $responseMessage = null;
        $params = $request->getQueryParams();
        $selected_tab = 'supplies';
        return $this->getRenderAgain($params, null, null, $selected_tab, $responseMessage);        
    }
    public function addComponentsSellOffersAction($request)
    {
        $responseMessage = null;
        $postData = $request->getParsedBody();
        $data = json_decode($postData['component']);        
        $offerComponent = new SellOffersComponents();
        $temp_component = SellOffersComponents::where('componentId', '=', $data->componentId)
                ->where('sellofferId', '=', $data->sellofferId);
        if($temp_component)
        {
            $offerComponent = $temp_component;
        }              
        $offerComponent->componentId = $data->componentId;
        $offerComponent->sellofferId = $data->sellofferId;
        $offerComponent->cantity = $data->cantity;
        $offerComponent->price = $data->price;
        if($temp_component)
        {
            $offerComponent->update();
            $responseMessage = 'Component Updated';
        }
        else
        {
            $offerComponent->save();
            $responseMessage = 'Component Saved';
        }
        $response = new JsonResponse($responseMessage);
        return $response;        
    }
    public function editComponentsSellOffersAction($request) {
        $responseMessage = 'Editando Component';
        $selected_tab = 'supplies';
        $params = $request->getQueryParams();       
        $component = DB::table('sellofferscomponents')                
                ->join('components', 'sellofferscomponents.componentId', '=', 'components.id')
                ->join('maders', 'components.mader', '=', 'maders.id')
                ->select('sellofferscomponents.id', 'sellofferscomponents.sellofferId', 'sellofferscomponents.componentId', 'components.ref as reference', 'maders.name as mader', 'components.name as name', 'sellofferscomponents.cantity as cantity', 'sellofferscomponents.price as price')
                ->where('sellofferscomponents.sellofferId', '=', $params['offer_id'])
                ->where('sellofferscomponents.componentId', '=', $params['component_id']);    
        if($component)
        {
            $array = (['component_price' => $component->price ,'component_cantity' => $component->cantity]);
            $params = array_merge($params, $array);
            $editComponent = SellOffersComponents::find($component->id);            
            if($editComponent)
            {
                $editComponent->delete();
            } 
        }           
        return $this->getRenderAgain($params, null, null, $selected_tab, $responseMessage);        
    }
    public function delComponentsSellOffersAction($request)
    {
        $responseMessage = 'Componente eliminado';
        $selected_tab = 'supplies';
        $params = $request->getQueryParams(); 
        
        $component = SellOffersComponents::where('componentId', '=', $params['component_id'])
                ->where('sellofferId', '=', $params['offer_id']);
        if($component)
        {
            $component->delete();
        }
        return $this->getRenderAgain($params, null, null, $selected_tab, $responseMessage);
    }
    public function selectWorksSellOffersAction($request)
    {
        $responseMessage = null;
        $params = $request->getQueryParams();        
        $selected_tab = 'works';
        return $this->getRenderAgain($params, null, null, $selected_tab, $responseMessage);
    }    
     public function searchWorksSellOffersAction($request)
    {        
        $searchString = null;
        $postData = $request->getParsedBody();
        if(isset($postData['searchFilter']))
        {
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
        $temp_work = SellOffersWorks::where('workId', '=', $data->workId)
                ->where('sellofferId', '=', $data->sellofferId);
        if($temp_work)
        {
            $work = $temp_work;
        }        
        $work->workId = $data->workId;
        $work->sellofferId = $data->sellofferId;
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
        $work = DB::table('selloffersworks')                
                ->join('works', 'selloffersworks.workId', '=', 'works.id')                
                ->select('selloffersworks.id', 'selloffersworks.sellofferId', 'selloffersworks.workId', 'works.reference', 'works.description', 'selloffersworks.cantity', 'selloffersworks.price')
                ->where('selloffersworks.sellofferId', '=', $params['offer_id'])
                ->where('selloffersworks.workId', '=', $params['work_id'])
                ->first();         
        if($work)
        {
            $array = (['work_price' => $work->price ,'work_cantity' => $work->cantity]);
            $params = array_merge($params, $array);
            $editWork = SellOffersWorks::find($work->id); 
            if($editWork)
            {
                $editWork->delete();
            } 
        }           
        return $this->getRenderAgain($params, null, null, $selected_tab, $responseMessage);        
    }
    public function delWorksSellOffersAction($request)
    {
        $responseMessage = 'Trabajo eliminado';
        $selected_tab = 'works';
        $params = $request->getQueryParams();        
        $work = SellOffersWorks::where('workId', '=', $params['work_id'])
                ->where('sellofferId', '=', $params['offer_id']);
        if($work)
        {
            $work->delete();
        }
        return $this->getRenderAgain($params, null, null, $selected_tab, $responseMessage);
    }
    public function getDetailedReportAction($request)
    {        
        $postData = $request->getParsedBody();
        $selected_accesories = $this->getSellOfferVehicleAccesories($postData['vehicle_id'])->toArray();        
        $offerSupplies = $this->getSellOfferSupplies($postData['id'])->toArray();
        $offerComponents = $this->getSellOfferComponents($postData['id'])->toArray();
        $offerWorks = $this->getSellOfferWorks($postData['id'])->toArray();
        array_push($postData, $offerSupplies, $offerComponents, $offerWorks, $selected_accesories);
        $report = new SellOfferDetailedReport();
        $report->AddPage();
        $report->Body($postData);        
        $report->Output();
    }
    public function getVehicleReportAction($request)
    {
        $postData = $request->getParsedBody();
        $selected_accesories = $this->getSellOfferVehicleAccesories($postData['vehicle_id'])->toArray();
        $newPostData = array_merge(['postData' => $postData, 'selected_accesories' => $selected_accesories]);
        $report = new SellOfferVehicleReport();
        $report->AddPage();
        $report->Body($newPostData);        
        $report->Output();
    }
    
    public function getRenderAgain($params, $new_offer, $offer_number, $selected_tab, $responseMessage)
    {        
        $selected_offer = $this->getSelectedOfferData($params);        
        if($new_offer === null)
        {
            if(isset($params['offer_id']) && $params['offer_id'])
            {
                $new_offer = $params['offer_id'];
            }
            else
            {
                $new_offer = 1;
            }            
        }        
        if($offer_number === null)           
        {
            if(DB::table('selloffers')->get()->last())
            {
                $last_offer = DB::table('selloffers')->get()->last();
                $offer_number = $this->getNewSellOfferNumber($last_offer);
            }
            else
            {
                $offer_number = $this->getFirstSellOfferNumber();
            }            
        }        
        $selectedSupply = $this->getSelectedOfferSelectedSupply($params);        
        $editPriceSupply = $selectedSupply->price;
        $editCantitySupply = $selectedSupply->cantity;           
        $selectedComponent = $this->getSelectedOfferComponents($params);
        $editPriceComponent = $selectedComponent->price;
        $editCantityComponent = $selectedComponent->cantity; 
        $selectedWork = $this->getSelectedOfferWorks($params);              
        $editPriceWork = $selectedWork->price;
        $editCantityWork = $selectedWork->cantity;        
        $selectedCustomer = $this->getSelectedOfferCustomer($params, $selected_offer);        
        $selectedVehicle = $this->getSelectedOfferVehicle($params, $selected_offer); 
        $brand = Brand::find($selectedVehicle->brand);
        $model = ModelVh::find($selectedVehicle->model);
        $selectedAccesories = $this->getSellOfferVehicleAccesories($selectedVehicle->id);
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
//        var_dump($selectedCustomer);die();
        return $this->renderHTML('/sells/offers/sellOffersForm.html.twig', [
            'sellOffer' => $selected_offer,
            'offer_number' => $offer_number,
            'customers' => $customers,
            'selected_customer' => $selectedCustomer,
            'vehicles' => $vehicles,
            'selected_vehicle' => $selectedVehicle,
            'brand' => $brand,
            'model' => $model,
            'types' => $types,
            'accesories' => $accesories,
            'selected_accesories' => $selectedAccesories,
            'supplies' => $supplies,
            'selected_supply' => $selectedSupply,
            'offerSupplies' => $offerSupplies,
            'edit_price_supply' => $editPriceSupply,
            'edit_cantity_supply' => $editCantitySupply,
            'selected_component' => $selectedComponent,
            'components' => $components, 
            'offerComponents' => $offerComponents,
            'edit_price_component' => $editPriceComponent,
            'edit_cantity_component' => $editCantityComponent,
            'selected_work' => $selectedWork,
            'selected_tab' => $selected_tab,                
            'works' => $works,                
            'offerWorks' => $offerWorks,
            'edit_price_work' => $editPriceWork,
            'edit_cantity_work' => $editCantityWork,
            'new_selloffer' => $new_offer,                
            'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
            'responseMessage' => $responseMessage
        ]);         
    }
    public function getFirstSellOfferNumber() 
    {        
        $offerString = '2020OF';        
        $offer_integer = 1;
        $offerStringNumber = strval($offer_integer);        
        while(strlen($offerStringNumber) < 4)
        {
            $offerStringNumber = "0".$offerStringNumber;                    
        }
        $offer_number = $offerString.$offerStringNumber;        
        return $offer_number;         
    }
    public function getNewSellOfferNumber($last_offer)
    {  
        $offerString = '2020OF';
        $offer_string = substr($last_offer->offerNumber, 6, 4);
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
    public function getSelectedOfferData($params)
    {                 
        $selected_offer = null;
        if(isset($params['offer_id'])&& $params['offer_id'])
        {           
            if(SellOffer::find($params['offer_id']))
            {
                $selected_offer = SellOffer::find($params['offer_id']);   
            }                    
        }        
        return $selected_offer;          
    }
    public function getSelectedOfferSelectedSupply($params)
    {              
        $selectedSupply = new SellOffersSupplies();
        if(isset($params['supply_id']) && $params['supply_id'] && $params['supply_id'] != 'null')
        {            
            $supply = Supplies::find($params['supply_id']);           
            $selectedSupply->supplyId = $params['supply_id'];
            $selectedSupply->offerId = $params['offer_id'];
            $selectedSupply->setAttribute('ref', $supply->ref);
            $selectedSupply->setAttribute('name', $supply->name);
            if(isset($params['supply_price']) && $params['supply_price'] && $params['supply_price'] != 'null')
            {
                $selectedSupply->price = $params['supply_price'];
            }
            else
            {               
                $selectedSupply->price = $supply->pvp;
            }
            if(isset($params['supply_cantity']) && $params['supply_cantity'] && $params['supply_cantity'] != 'null')
            {
               $selectedSupply->cantity = $params['supply_cantity'];
            }
            else
            {
                $selectedSupply->cantity = 0;
            }
        }        
        return $selectedSupply;
    }    
    public function getSelectedOfferComponents($params)
    {
        $selected_component = new SellOffersComponents();        
        if(isset($params['component_id']) && $params['component_id'] && $params['component_id'] !== 'null')
        {
            $component = Components::find($params['component_id']);
            $selected_component->offerId = $params['offer_id'];
            $selected_component->componentId = $params['component_id'];
            $selected_component->setAttribute('ref', $component->ref);
            $selected_component->setAttribute('name', $component->name);
            if(isset($params['component_price']) && $params['component_price'] && $params['component_price'] != 'null')
            {               
                $selected_component->price = $params['component_price'];                
            }
            else
            {
                $selected_component->price = $component->pvp;
            }
            if(isset($params['component_cantity']) && $params['component_cantity'] && $params['component_cantity'] != 'null')
            {            
               $selected_component->cantity = $params['component_cantity'];                
            } 
            else
            {
                $selected_component->cantity = 0;
            }
        }      
        return $selected_component;
    }
    public function getSelectedOfferWorks($params)
    {
        $selectedWork = new SellOffersWorks();        
        if(isset($params['work_id']) && $params['work_id'] && $params['work_id'] !== 'null')
        {           
            $selectedWork->workId = $params['work_id'];
            $selectedWork->sellofferId = $params['offer_id'];
            $work = Works::find($params['work_id'])->first();
            $selectedWork->setAttribute('reference', $work->reference);
            $selectedWork->setAttribute('description', $work->description);
            if(isset($params['work_price']) && $params['work_price'] && $params['work_price'] != 'null')
            {                
                $selectedWork->price = $params['work_price'];                
            } 
            else
            {
                $selectedWork->price = $work->pvp;
            }
            if(isset($params['work_cantity']) && $params['work_cantity'] && $params['work_cantity'] != 'null')
            {
                $selectedWork->cantity = $params['work_cantity'];             
            }
            else
            {
                $selectedWork->cantity = 0;
            }
        }
        return $selectedWork;
    }
    public function getSelectedOfferCustomer($params, $selected_offer)
    {
        $selected_customer = null;       
        if(isset($params['customer_id']) && $params['customer_id'] && $params['customer_id'] != 'null')
        {            
            
            $selected_customer = Customer::find($params['customer_id']);            
        } 
        else
        {   
            if($selected_offer)
            {
                $selected_customer = Customer::find($selected_offer->customerId);
            }         
            
        }
        return $selected_customer;
    }
    public function getSelectedOfferVehicle($params, $selected_offer)
    {
        $selected_vehicle = new Vehicle();       
        if(isset($params['vehicle_id']) && $params['vehicle_id'] && $params['vehicle_id'] != 'null')
        {
            if(Vehicle::find($params['vehicle_id']))
            {
                $selected_vehicle = Vehicle::find($params['vehicle_id']); 
            }
        }
        else
        {
            if($selected_offer)
            {
                $selected_vehicle = Vehicle::find($selected_offer->vehicleId);
            }
            
        }       
        return $selected_vehicle;
    }  
    public function getSellOfferSupplies($offer_id)
    {
        $offerSupplies = DB::table('sellofferssupplies')                
            ->join('supplies', 'sellofferssupplies.supplyId', '=', 'supplies.id')
            ->join('maders', 'supplies.mader', '=', 'maders.id')
            ->select('sellofferssupplies.id', 'sellofferssupplies.sellofferId', 'sellofferssupplies.supplyId', 'supplies.ref as reference', 'maders.name as mader', 'supplies.name as name', 'sellofferssupplies.cantity as cantity', 'sellofferssupplies.price as price')
            ->where('sellofferssupplies.sellofferId', '=', $offer_id)
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
        $offerComponents = DB::table('sellofferscomponents')
            ->join('components', 'sellofferscomponents.componentId', '=', 'components.id')
            ->join('maders', 'components.mader', '=', 'maders.id')
            ->select('sellofferscomponents.id', 'sellofferscomponents.sellofferId', 'sellofferscomponents.componentId', 'components.ref as reference', 'components.name as name', 'sellofferscomponents.cantity as cantity', 'sellofferscomponents.price as price')
            ->where('sellofferscomponents.sellofferId', '=', $offer_id)                
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
        $offerWorks = DB::table('selloffersworks')
            ->join('works', 'selloffersworks.workId', '=', 'works.id')               
            ->select('selloffersworks.id', 'selloffersworks.sellofferId', 'selloffersworks.workId', 'works.description as description', 'selloffersworks.cantity as cantity', 'selloffersworks.price as price')
            ->where('selloffersworks.sellofferId', '=', $offer_id)                
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
        $selected_accesories = DB::table('vehicleaccesories')
                ->join('accesories', 'vehicleaccesories.accesoryId', '=', 'accesories.id')
                ->select('vehicleaccesories.accesoryId','vehicleaccesories.id', 'accesories.keystring', 'accesories.name')
                ->where('vehicleaccesories.vehicleId', '=', $vehicle_id)
                ->get();
        
        return $selected_accesories;
    }
}
