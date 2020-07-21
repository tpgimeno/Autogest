<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Controllers\Crm;

use App\Controllers\BaseController;
use App\Models\Customer;
use App\Models\SellOffer;
use App\Models\Vehicle;
use App\Services\Crm\SellOfferService;
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
    
    public function getIndexAction()
    {
        $offers = SellOffer::All();
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
    public function selectCustomerSellOfferAction($request)
    {
        $responseMessage = null;
        $params = $request->getQueryParams();
        $customer = null;
//        var_dump($params);die;
        if($params['customer_id'])
        {
            $customer = Customer::find($params['customer_id'])->first();
        }
        $selected_offer = null;
        if(isset($params['offer_id']))
        {
            if($params['offer_id'])
            {
                $selected_offer = SellOffer::find($params['offer_id'])->first();
            }
        }        
        $vehicle = null;
        if(isset($params['vehicle_id']))
        {
            if($params['vehicle_id'])
            {
                $vehicle = Vehicle::find($params['vehicle_id'])->first();
            }
        }
        
        $customers = Customer::All();
        $vehicles = Vehicle::All();
        return $this->renderHTML('/sells/offers/sellOffersForm.html.twig', [
            'sellOffer' => $selected_offer,
            'customers' => $customers,
            'customer' => $customer,
            'vehicles' => $vehicles,
            'vehicle' => $vehicle,
            'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
            'responseMessage' => $responseMessage
        ]);
    }
    public function searchSellOffersAction($request)
    {
        
    }
    public function getSellOfferDataAction($request)
    {
        $responseMessage = null;
        if($request->getMethod() == 'POST')
        {
            $postData = $request->getParsedBody();
            $offerValidator = v::key('order_number', v::stringType()->noEmpty())
                    ->key('customer_id', v::noEmpty())
                    ->key('vehicle_id', v::noEmpty());
            try
            {
                 $offerValidator->assert($postData);
                 $offer = new SellOffer();
                 $offer->id = $postData['id'];
                 if($offer->id)
                 {
                     $offer_temp = SellOffer::find($offer->id)->first();
                     $offer = $offer_temp;
                 }
                 $offer->offer_number = $postData['offer_number'];
                 $customer = Customer::where('name', 'LIKE', $postData['name'])->first();
                 $offer->customer_id = $customer->id;
                 $vehicle = Vehicle::where('plate', 'LIKE', $postData['plate'])->first();
                 $offer->vehicle_id = $vehicle->id;                 
                 $offer->discount = $postData['discount'];
                 $offer->pvp = $postData['pvp'] - $postData['discount'];
                 $offer->tva = $offer->pvp * 0.21;
                 $offer->total = $offer->pvp + $offer->tva;
                 $offer->observations = $postData['observations'];
                 $offer->texts = $postData['texts'];
                 if($offer->id)
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
        if($request->getQueryParams())
        {
            $selected_offer = SellOffer::find($request->getQueryParams('id'))->first();
        }
        $customers = Customer::All();
        $vehicles = Vehicle::All();
        return $this->renderHTML('/sells/offers/sellOffersForm.html.twig', [
            'sellOffer' => $selected_offer,
            'customers' => $customers,
            'vehicles' => $vehicles,
            'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
            'responseMessage' => $responseMessage
        ]);
    }
}
