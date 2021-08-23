<?php

namespace App\Controllers\Buys;

use App\Controllers\BaseController;
use Respect\Validation\Validator as v;
use App\Models\BuyDelivery;
use App\Services\Buys\BuyDeliveryService;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Response\RedirectResponse;
use Illuminate\Database\Capsule\Manager as DB;

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
        $buyDeliveries = DB::table('buy_delivery')
                ->join('providers', 'buy_delivery.providor_id', '=', 'providers.id')
                ->select('buy_delivery.id', 'buy_delivery.delivery_number', 'buy_delivery.date', 'providers.name', 'buy_delivery.total')                
                ->whereNull('buy_delivery.deleted_at')
                ->get();
        return $this->renderHTML('/buys/buy_deliveryList.html.twig', [
            'buyDeliveries' => $buyDeliveries
        ]);
    }       
    public function searchBuyDeliveriesAction($request)
    {
        $postData = $request->getParsedBody();
        $searchString = $postData['searchFilter'];
        $buyDeliveries = DB::table('buy_delivery')
                ->join('providers', 'buy_delivery.providor_id', '=', 'providers.id')
                ->select('buy_delivery.id', 'buy_delivery.delivery_number', 'buy_delivery.date', 'providers.name', 'buy_delivery.total')
                ->where('buy_delivery.delivery_number', 'like', "%".$searchString."%")
                ->orWhere('providers.name', 'like', "%".$searchString."%")
                ->orWhere('buy_delivery.date', 'like', "%".$searchString."%")
                ->whereNull('buy_delivery.deleted_at')
                ->get();
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
                $buyDelivery->id = $postData['id'];
                if($buyDelivery->id)
                {
                    $buyDelivery_temp = BuyDelivery::find($buyDelivery->id)->first();
                    if($buyDelivery_temp)
                    {
                        $buyDelivery = $buyDelivery_temp;
                    }
                }
                $buyDelivery->buyDelivery_number = $postData['delivery_number'];
                $buyDelivery->date = $postData['date'];
                $buyDelivery->providor_id = $postData['providor_id'];
                $buyDelivery->articles = $postData['city'];
                $buyDelivery->base = $postData['postal_code'];
                $buyDelivery->tva = $postData['tva'];
                $buyDelivery->total = $postData['total'];
                $buyDelivery->observations = $postData['observations'];
                $buyDelivery->text = $postData['text'];
                if($buyDelivery_temp)
                {
                    $buyDelivery->update();
                    $responseMessage = 'Updated';
                }
                else
                {
                    $buyDelivery->save();     
                    $responseMessage = 'Saved';
                }                     
            }catch(\Exception $e){                
                $responseMessage = $e->getMessage();
            }              
        }
        $buyDeliverySelected = null;
        if($request->getQueryParams('id'))
        {
            $buyDeliverySelected = BuyDeliveries::find($request->getQueryParams('id'));
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
        return new RedirectResponse('/Intranet/buys/deliveries/list');
    }

   

}
