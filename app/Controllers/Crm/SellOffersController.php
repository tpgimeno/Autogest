<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Controllers\Crm;

use App\Controllers\BaseController;
use App\Models\Brand;
use App\Models\Components;
use App\Models\Customer;
use App\Models\ModelVh;
use App\Models\SellOffer;
use App\Models\SellOffersComponents;
use App\Models\SellOffersSupplies;
use App\Models\Supplies;
use App\Models\Vehicle;
use App\Models\VehicleTypes;
use App\Reports\SellOfferReport;
use App\Services\Crm\SellOfferService;
use Illuminate\Database\Capsule\Manager as DB;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Validator as v;
use Symfony\Component\Config\Definition\Exception\Exception;


/**
 * Description of SellOffersController
 *
 * @author tonyl
 */
class SellOffersController extends BaseController
{
    
    protected $sellOfferService;
    
    public function __construct(SellOfferService $sellOfferService) {
        parent::__construct();
        $this->sellOfferService = $sellOfferService;
    }
//    Funcion que muestra la lista de ofertas
    public function getIndexAction()
    {
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
                ->select('vehicles.id as id', 'vehicles.plate as plate', 'vehicles.vin as vin', 
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
    public function selectVehicleSellOfferAction($request)
    {
        $responseMessage = null;
        $params = $request->getQueryParams();
        $customer = null;
//        var_dump($params);die;
        if(isset($params['customer_id']))
        {
            $customer = Customer::where('id', $params['customer_id'])->first();
        }
        $selected_offer = null;
        if(isset($params['offer_id']))
        {
            if($params['offer_id'])
            {
                $selected_offer = SellOffer::where('id', $params['offer_id'])->first();
            }
        }
        if($selected_offer === null)
        {
            $last_offer = SellOffer::All()->last();
            if($last_offer === null)
            {
                $new_offer = 1;
            }
            else
            {
                $new_offer = $last_offer->id + 1;
            }
        }
        else
        {
            $new_offer = $selected_offer->id;
        }
        $vehicle = null;
        if(isset($params['vehicle_id']))
        {
            if($params['vehicle_id'])
            {
                $vehicle = Vehicle::where('id', $params['vehicle_id'])->first();
                $brand = Brand::find($vehicle->brand)->first();
                $model = ModelVh::find($vehicle->model)->first();
            }
        }
        $offerSupplies = DB::table('selloffers_supplies')                
            ->join('supplies', 'selloffers_supplies.supply_id', '=', 'supplies.id')
            ->join('maders', 'supplies.mader', '=', 'maders.id')
            ->select('supplies.id', 'supplies.ref as reference', 'maders.name as mader', 'supplies.name as name', 'selloffers_supplies.cantity as cantity', 'selloffers_supplies.price as price')
            ->where('selloffers_supplies.selloffer_id', '=', $params['offer_id'])                      
            ->get();

        $offerComponents = DB::table('selloffers_components')
            ->join('components', 'selloffers_components.component_id', '=', 'components.id')
            ->join('maders', 'components.mader', '=', 'maders.id')
            ->select('selloffers_components.id', 'components.ref as reference', 'components.name as name', 'selloffers_components.cantity as cantity', 'selloffers_components.price as price')
            ->where('selloffers_components.selloffer_id', '=', $params['offer_id'])
            ->get();
        $customers = Customer::All();
        $vehicles = Vehicle::All();
        $types = VehicleTypes::All();
        $supplies = Supplies::All();
        $components = Components::All();
        return $this->renderHTML('/sells/offers/sellOffersForm.html.twig', [
            'sellOffer' => $selected_offer,
            'customers' => $customers,
            'customer' => $customer,
            'vehicles' => $vehicles,
            'vehicle' => $vehicle,
            'brand' => $brand,
            'model' => $model,
            'types' => $types,
            'supplies' => $supplies,
            'components' => $components,
            'offerSupplies' => $offerSupplies,
            'offerComponents' => $offerComponents,
            'new_selloffer' => $new_offer,
            'selected_tab' => 'vehicle',
            'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
            'responseMessage' => $responseMessage
        ]);
    }
    public function selectCustomerSellOfferAction($request)
    {
        $responseMessage = null;
        $params = $request->getQueryParams();
        $customer = null;
        $accesory = 'components';
        if($params['customer_id'])
        {
            $customer = Customer::where('id', $params['customer_id'])->first();
        }        
        
        $selected_offer = null;
        if(isset($params['offer_id']))
        {
            if($params['offer_id'])
            {
                $selected_offer = SellOffer::where('id', $params['offer_id'])->first();
            }
        }
        if($selected_offer === null)
        {
            $last_offer = SellOffer::All()->last();
            if($last_offer === null)
            {
                $new_offer = 1;
            }
            else
            {
                $new_offer = $last_offer->id + 1;
            }
        }
        else
        {
            $new_offer = $selected_offer->id;
        }
        $vehicle = null;
        $brand = null;
        $model = null;
        if(isset($params['vehicle_id']))
        {
            if($params['vehicle_id'])
            {
                $vehicle = Vehicle::where('id', $params['vehicle_id'])->first();
                $brand = Brand::find($vehicle->brand)->first();
                $model = ModelVh::find($vehicle->model)->first();
            }
        }
       
        $offerSupplies = DB::table('selloffers_supplies')                
            ->join('supplies', 'selloffers_supplies.supply_id', '=', 'supplies.id')
            ->join('maders', 'supplies.mader', '=', 'maders.id')
            ->select('supplies.id', 'supplies.ref as reference', 'maders.name as mader', 'supplies.name as name', 'selloffers_supplies.cantity as cantity', 'selloffers_supplies.price as price')
            ->where('selloffers_supplies.selloffer_id', '=', $params['offer_id'])               
            ->get();
//            var_dump(((array)$offerSupplies));die();
        $offerComponents = DB::table('selloffers_components')
            ->join('components', 'selloffers_components.component_id', '=', 'components.id')
            ->join('maders', 'components.mader', '=', 'maders.id')
            ->select('selloffers_components.id', 'components.ref as reference', 'components.name as name', 'selloffers_components.cantity as cantity', 'selloffers_components.price as price')
            ->where('selloffers_components.selloffer_id', '=', $params['offer_id'])
            ->get();        
        $customers = Customer::All();
        $vehicles = Vehicle::All();
        $types = VehicleTypes::All();
        $supplies = Supplies::All();
        $components = Components::All();        
        return $this->renderHTML('/sells/offers/sellOffersForm.html.twig', [
            'sellOffer' => $selected_offer,
            'customers' => $customers,
            'customer' => $customer,
            'vehicles' => $vehicles,
            'vehicle' => $vehicle,
            'brand' => $brand,
            'model' => $model,
            'types' => $types,
            'supplies' => $supplies,
            'components' => $components,
            'offerSupplies' => $offerSupplies,
            'offerComponents' => $offerComponents,
            'accesory' => $accesory,
            'new_selloffer' => $new_offer,
            'selected_tab' => 'customer',
            'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
            'responseMessage' => $responseMessage
        ]);
    }
    public function searchSuppliesSellOffersAction($request)
    {
        $responseMessage = null;
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
    public function searchComponentsSellOffersAction($request)
    {
        $responseMessage = null;
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
    public function selectSuppliesSellOffersAction($request)
    {
        $responseMessage = null;        
        $params = $request->getQueryParams();       
        $offer = null;
        if(isset($params['offer_id']))
        {
            if($params['offer_id'])
            {
                $offer = SellOffer::find($params['offer_id']);
            }
        }
        if($offer === null)
        {
            $last_offer = SellOffer::All()->last();
            if($last_offer === null)
            {
                $new_offer = 1;
            }
            else
            {
                $new_offer = $last_offer->id + 1;
            }
        }
        else
        {
            $new_offer = $offer->id;
        }
        $component = null;
        if(isset($params['component_id']))
        {
            if($params['component_id'])
            {
                $component = Components::find($params['component_id']);
            }
        }
        $customer = null;
        if(isset($params['customer_id']))
        {
            if($params['customer_id'])
            {
                $customer = Customer::find($params['customer_id']);
            }
        }
        $vehicle = null;
        $brand = null;
        $model = null;
        if(isset($params['vehicle_id']))
        {
            if($params['vehicle_id'])
            {
                $vehicle = Vehicle::find($params['vehicle_id']);
                $brand = Brand::find($vehicle->brand)->first();
                $model = ModelVh::find($vehicle->model)->first();
            }
        }
        try
        {
            $offerSupplies = DB::table('selloffers_supplies')                
                ->join('supplies', 'selloffers_supplies.supply_id', '=', 'supplies.id')
                ->join('maders', 'supplies.mader', '=', 'maders.id')
                ->select('supplies.id', 'supplies.ref as reference', 'maders.name as mader', 'supplies.name as name', 'selloffers_supplies.cantity as cantity', 'selloffers_supplies.price as price')
                ->where('selloffers_supplies.selloffer_id', '=', $params['offer_id'])
                ->where('selloffers_supplies.supply_id', '=', $params['supply_id'])               
                ->get();
//            var_dump(((array)$offerSupplies));die();
            $offerComponents = DB::table('selloffers_components')
                ->join('components', 'selloffers_components.component_id', '=', 'components.id')
                ->join('maders', 'components.mader', '=', 'maders.id')
                ->select('selloffers_components.id', 'components.ref as reference', 'components.name as name', 'selloffers_components.cantity as cantity', 'selloffers_components.price as price')
                ->where('selloffers_components.selloffer_id', '=', $params['offer_id'])
                ->where('selloffers_components.component_id', '=', "%".$params['component_id']."%")
                ->get();
            $selected_supply = null;
            
            if($params['supply_id'])
            {
                $selected_supply = Supplies::find($params['supply_id']); 
            }                       
            $types = VehicleTypes::All();
            $supplies = Supplies::All();
            $components = Components::All();
            $customers = Customer::All();
            $vehicles = Vehicle::All();
            return $this->renderHTML('/sells/offers/sellOffersForm.html.twig', [
                'sellOffer' => $offer,
                'customers' => $customers,
                'customer' => $customer,
                'vehicles' => $vehicles,
                'vehicle' => $vehicle,
                'brand' => $brand,
                'model' => $model,
                'types' => $types,
                'supplies' => $supplies,
                'selected_supply' => $selected_supply,
                'selected_tab' => 'supplies',
                'components' => $components,                
                'offerSupplies' => $offerSupplies,
                'new_selloffer' => $new_offer,
                'offerComponents' => $offerComponents,
                'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
                'responseMessage' => $responseMessage
            ]); 
        } catch (Exception $ex) 
        {
              $responseMessage = $ex->getMessage();         
        }
    }
    public function addSuppliesSellOffersAction($request)
    {
        $responseMessage = null;
        $postData = $request->getParsedBody();
        $data = explode(',', $postData['supplies']);
        $supply = new SellOffersSupplies();
        $temp_supply = SellOffersSupplies::where('supply_id', '=', $data[1])
                ->where('selloffer_id', '=', $data[0])
                ->first();
        if($temp_supply)
        {
            $supply = $temp_supply;
        }        
        $supply->supply_id = $data[1];
        $supply->selloffer_id = $data[0];
        $supply->cantity = $data[5];
        $supply->price = $data[4];
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
    public function addComponentsSellOffersAction($request)
    {
        $responseMessage = null;
        $postData = $request->getParsedBody();
        $data = explode(',', $postData['components']);
        $component = new SellOffersComponents();
        $temp_component = SellOffersComponents::where('component_id', '=', $data[1])
                ->where('selloffer_id', '=', $data[0])
                ->first();
        if($temp_component)
        {
            $component = $temp_component;
        }        
        $component->component_id = $data[1];
        $component->selloffer_id = $data[0];
        $component->cantity = $data[5];
        $component->price = $data[4];
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
    public function selectComponentsSellOffersAction($request)
    {
        $responseMessage = null;        
        $params = $request->getQueryParams();
        $offer = new SellOffer();
        $accesory = 'components';
        if(isset($params['offer_id']))
        {
            if($params['offer_id'])
            {
                $offer = SellOffer::find($params['offer_id']);                
                if($offer)
                {
                    $offer = SellOffer::find($params['offer_id'])->first();  
                }
            }
        }
        if($offer === null)
        {
            $last_offer = SellOffer::All()->last();
            if($last_offer === null)
            {
                $new_offer = 1;
            }
            else
            {
                $new_offer = $last_offer->id + 1;
            }
        }
        else
        {
            $new_offer = $offer->id;
        }
        $component = null;
        if(isset($params['component_id']))
        {
            if($params['component_id'])
            {
                $component = Components::find($params['component_id'])->first();
            }
        }
        $customer = null;
        if(isset($params['customer_id']))
        {
            if($params['customer_id'])
            {
                $customer = Customer::find($params['customer_id'])->first();
            }
        }
        $vehicle = null;
        $brand = null;
        $model = null;
        if(isset($params['vehicle_id']))
        {
            if($params['vehicle_id'])
            {
                $vehicle = Vehicle::find($params['vehicle_id'])->first();
                $brand = Brand::find($vehicle->brand)->first();
                $model = ModelVh::find($vehicle->model)->first();
            }
        }
        try
        {
            $offerSupplies = DB::table('selloffers_supplies')                
                ->join('supplies', 'selloffers_supplies.supply_id', '=', 'supplies.id')
                ->join('maders', 'supplies.mader', '=', 'maders.id')
                ->select('supplies.id', 'supplies.ref as reference', 'maders.name as mader', 'supplies.name as name', 'selloffers_supplies.cantity as cantity', 'selloffers_supplies.price as price')
                ->where('selloffers_supplies.selloffer_id', '=', $params['offer_id'])                              
                ->get();
//            var_dump($offerSupplies);die();
            $offerComponents = DB::table('selloffers_components')
                ->join('components', 'selloffers_components.component_id', '=', 'components.id')
                ->join('maders', 'components.mader', '=', 'maders.id')
                ->select('selloffers_components.id', 'components.ref as reference', 'components.name as name', 'selloffers_components.cantity as cantity', 'selloffers_components.price as price')
                ->where('selloffers_components.selloffer_id', '=', $params['offer_id'])                
                ->get();
            if(isset($params['component_id']))
            {
                $selected_component = Components::find($params['component_id'])->first();
            }                        
            $types = VehicleTypes::All();
            $supplies = Supplies::All();
            $components = Components::All();
            $customers = Customer::All();
            $vehicles = Vehicle::All();
            return $this->renderHTML('/sells/offers/sellOffersForm.html.twig', [
                'sellOffer' => $offer,
                'customers' => $customers,
                'customer' => $customer,
                'vehicles' => $vehicles,
                'vehicle' => $vehicle,
                'brand' => $brand,
                'model' => $model,
                'types' => $types,
                'supplies' => $supplies,               
                'selected_tab' => 'supplies',
                'components' => $components,
                'accesory' => $accesory,
                'selected_component' => $selected_component,
                'offerSupplies' => $offerSupplies,
                'new_selloffer' => $new_offer,
                'offerComponents' => $offerComponents,
                'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
                'responseMessage' => $responseMessage
            ]); 
        } catch (Exception $ex) 
        {
              $responseMessage = $ex->getMessage();         
        }
    }
    
    public function searchSellOffersAction($request)
    {
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
    public function getSellOffersDataAction($request)
    {
        $responseMessage = null;
        if($request->getMethod() == 'POST')
        {
            $postData = $request->getParsedBody();
            $offerValidator = v::key('offer_number', v::stringType()->notEmpty())
                    ->key('customer_id', v::notEmpty())
                    ->key('vehicle_id', v::notEmpty());
            try
            {
                 $offerValidator->assert($postData);
                 $offer = new SellOffer();
                 $offer->id = $postData['id'];
                 $offer_selected = false;
                 if($offer->id)
                 {
                     $offer_temp = SellOffer::find($offer->id)->first();
                     if($offer_temp)
                     {
                         $offer = $offer_temp;
                         $offer_selected = true;
                     }                    
                 }
                 $offer->offer_number = $postData['offer_number'];
                 $offer->offer_date = $postData['date'];
                 $customer = Customer::where('name', 'LIKE', $postData['name'])->first();
                 $offer->customer_id = $customer->id;
                 $vehicle = Vehicle::where('plate', 'LIKE', $postData['plate'])->first();
                 $offer->vehicle_id = $vehicle->id;                 
                 $offer->discount = $postData['discount'];                 
                 $offer->pvp = intval($postData['price']) - intval($postData['discount']);
                 $offer->tva = intval($offer->pvp) * 0.21;
                 $offer->total = intval($offer->pvp) + intval($offer->tva);
                 $offer->observations = $postData['observations'];
                 $offer->texts = $postData['texts'];
                 $offer->vehicle_comments = $postData['vehicle_comments'];
                 if($offer_selected == true)
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
            catch (Exception $ex) 
            {
                $responseMessage = $ex->getMessage();
            }            
        }
        $selected_offer = null;
        $last_offer = null;
        $new_offer = null;
        if($request->getQueryParams())
        {
            $selected_offer = SellOffer::find($request->getQueryParams('id'))->first();
        }
        if($selected_offer === null)
        {
            $last_offer = SellOffer::All()->last();
            if($last_offer === null)
            {
                $new_offer = 1;
            }
            else
            {
                $new_offer = $last_offer->id + 1;
            }
        }
        else
        {
            $new_offer = $selected_offer->id;
        }
        
        $selected_customer = null;
        $selected_vehicle = null;
        $selected_supplies = null;
        $selected_components = null;
        $brand = null;
        $model = null;
        if($selected_offer)
        {
            $selected_customer = Customer::find($selected_offer->customer_id)->first();
            $selected_vehicle = Vehicle::find($selected_offer->vehicle_id)->first();
            $brand = Brand::find($selected_vehicle->brand)->first();
            $model = ModelVh::find($selected_vehicle->model)->first();
            $selected_supplies = DB::table('selloffers_supplies')
                ->join('supplies', 'selloffers_supplies.supply_id', '=', 'supplies.id')
                ->select('selloffers_supplies.id', 'supplies.ref', 'supplies.name', 'supplies.pvp')
                ->where('selloffers_supplies.selloffer_id', '=', $selected_offer->id)
                ->get();
            $selected_components = DB::table('selloffers_components')
                    ->join('components', 'selloffers_components.component_id', '=', 'components.id')
                    ->select('selloffers_components.id', 'components.ref', 'components.name', 'components.pvp')
                    ->where('selloffers_components.selloffer_id', '=', $selected_offer->id)
                    ->get();
        }
        
        $types = VehicleTypes::All();
        $supplies = Supplies::All();
        $components = Components::All();
        $customers = Customer::All();
        $vehicles = Vehicle::All();
        return $this->renderHTML('/sells/offers/sellOffersForm.html.twig', [
            'sellOffer' => $selected_offer,
            'customers' => $customers,
            'customer' => $selected_customer,
            'vehicles' => $vehicles,
            'vehicle' => $selected_vehicle,
            'brand' => $brand,
            'model' => $model,
            'types' => $types,
            'supplies' => $supplies,
            'selected_supplies' => $selected_supplies,
            'selected_components' => $selected_components,
            'new_selloffer' => $new_offer,
            'components' => $components,
            'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
            'responseMessage' => $responseMessage
        ]);
    }
    public function getReportAction($request)
    {
        $postData = $request->getParsedBody();
        var_dump($postData);die();
        $report = new SellOfferReport();
        $report->AddPage();
        $report->Body($postData);
        $report->Output();
    }
}
