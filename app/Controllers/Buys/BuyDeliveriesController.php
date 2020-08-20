<?php

namespace App\Controllers\Buys;

use App\Controllers\BaseController;
use Respect\Validation\Validator as v;
use App\Models\BuyDelivery;
use App\Services\Buys\BuyDeliveryService;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Response\RedirectResponse;

class BuyDeliveriesController extends BaseController
{    
    
    protected $buyDeliveryService;

    public function __construct(BuyDeliveryService $buyDeliveryService)
    {
        parent::__construct();
        $this->buyDeliveryService = $buyDeliveryService;
    }    
    public function getIndexAction()
    {
        $buyDeliveries = BuyDelivery::All();
        return $this->renderHTML('/buys/buy_deliveryList.html.twig', [
            'buyDeliveries' => $buyDeliveries
        ]);
    }   
    
    
    
    public function getBuyDeliveriesDataAction($request)
    {                
        $responseMessage = null;
        if($request->getMethod() == 'POST')
        {
            $postData = $request->getParsedBody();
            
            $buyDeliveryValidator = v::key('name', v::stringType()->notEmpty()) 
            ->key('fiscal_id', v::notEmpty())
            ->key('phone', v::notEmpty())
            ->key('email', v::stringType()->notEmpty());            
            try{
                $buyDeliveryValidator->assert($postData); // true 
                $buyDelivery = new BuyDeliveries();
                $buyDelivery->name = $postData['name'];
                $buyDelivery->fiscal_id = $postData['fiscal_id'];
                $buyDelivery->fiscal_name = $postData['fiscal_name'];
                $buyDelivery->address = $postData['address'];
                $buyDelivery->city = $postData['city'];
                $buyDelivery->postal_code = $postData['postal_code'];
                $buyDelivery->state = $postData['state'];
                $buyDelivery->country = $postData['country'];
                $buyDelivery->phone = $postData['phone'];
                $buyDelivery->email = $postData['email'];
                $buyDelivery->site = $postData['site'];
                $buyDelivery->save();     
                $responseMessage = 'Saved';     
            }catch(\Exception $e){                
                $responseMessage = $e->getMessage();
            }              
        }
        $buyDeliverySelected = null;
        if($_GET)
        {
            $buyDeliverySelected = BuyDeliveries::find($_GET['id']);
        }
        return $this->renderHTML('/buys/buy_deliveryForm.html.twig', [
            'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
            'responseMessage' => $responseMessage,
            'buyDelivery' => $buyDeliverySelected
        ]);
    }

    public function deleteAction(ServerRequest $request)
    {
         
        $this->buyDeliveryService->deleteBuyDeliveries($request->getQueryParams('id'));               
        return new RedirectResponse('/intranet/buys/deliveries/list');
    }

   

}