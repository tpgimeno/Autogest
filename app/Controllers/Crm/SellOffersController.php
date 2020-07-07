<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Controllers\Crm;

use App\Controllers\BaseController;
use App\Models\SellOffer;
use App\Services\Crm\SellOfferService;
use Respect\Validation\Validator as v;

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
    public function getSearchCustomerSellOffersAction($request)
    {
        $customer = null;
        $postData = $request->getParsedBody();
        $searchString = $postData['searchCustomerFilter'];
        $customer = Customer::Where("name", "like", "%".$searchString."%" )
                ->orWhere("fiscal_id", "like", "%".$searchString."%")
                ->orWhere("address", "like", "%".$searchString."%")
                ->orWhere("phone", "like", "%".$searchString."%")
                ->orWhere("email", "like", "%".$searchString."%")
                ->get();
        return $this->renderHTML('/sells/offers/sellOffersForm.html.twig', [
            'sellOffer' => $selected_offer,
            'customer' => $customer,            
            'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
            'responseMessage' => $responseMessage
        ]);
        
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
        return $this->renderHTML('/sells/offers/sellOffersForm.html.twig', [
            'sellOffer' => $selected_offer,
            'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
            'responseMessage' => $responseMessage
        ]);
    }
}
