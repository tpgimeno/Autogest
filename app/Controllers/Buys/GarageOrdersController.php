<?php

namespace App\Controllers\Buys;

use App\Controllers\BaseController;
use Respect\Validation\Validator as v;
use App\Models\GarageOrder;
use App\Models\Vehicle;
use App\Models\Customer;
use App\Services\Buys\GarageOrderService;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Response\RedirectResponse;

class GarageOrdersController extends BaseController
{    
    
    protected $GarageOrderService;
    protected $GarageOrderSelected;
    protected $works;
    

    public function __construct(GarageOrderService $GarageOrderService)
    {
        parent::__construct();
        $this->GarageOrderService = $GarageOrderService;       
    }
    
    
    
    public function getIndexAction()
    {
        $GarageOrders = GarageOrder::All();
        return $this->renderHTML('/buys/garageOrdersList.twig', [
            'GarageOrders' => $GarageOrders
        ]);
    }    
    
    public function getOrderDataAction($request)
    {                
        $responseMessage = null;
        $this->GarageOrderSelected = new GarageOrder();
        if($request->getMethod() == 'POST')
        {
            $postData = $request->getParsedBody();            
            // $GarageOrderValidator = v::key('name', v::stringType()->notEmpty()) 
            // ->key('fiscal_id', v::notEmpty())
            // ->key('phone', v::notEmpty())
            // ->key('email', v::stringType()->notEmpty());            
            try{
                // $GarageOrderValidator->assert($postData); // true 
                $GarageOrder = new GarageOrder();
                $GarageOrder->order_number = $postData['order_number'];
                $GarageOrder->date_in = $postData['date_in'];
                $GarageOrder->date_out = $postData['date_out'];
                $GarageOrder->vehicle_id = $postData['vehicle_id'];
                $GarageOrder->customer_id = $postData['customer_id'];
                $GarageOrder->km_in = $postData['km_in'];
                $GarageOrder->km_out = $postData['km_out'];
                $GarageOrder->works = $postData['works'];
                $GarageOrder->articles = $postData['articles'];
                $GarageOrder->price_works = $postData['price_works'];
                $GarageOrder->price_articles = $postData['price_articles'];
                $GarageOrder->price_articles = $postData['price_articles'];
                $GarageOrder->observations = $postData['observations'];
                $GarageOrder->text = $postData['text'];
                $GarageOrder->save();     
                $responseMessage = 'Saved'; 
                $this->GarageOrderSelected = $GarageOrder;    
            }catch(\Exception $e){                
                $responseMessage = $e->getMessage();
            }              
        }
        $Vehicle = new Vehicle();
        $Customer = new Customer();
        if($_GET)
        {             
            $this->GarageOrderSelected = GarageOrder::find($_GET['id']);            
            if($this->GarageOrderSelected)
            {
                $Vehicle = Vehicle::find($this->GarageOrderSelected["vehicle_id"]);
                $Customer = Customer::find($this->GarageOrderSelected['customer_id']);               
            }            
        }
        // var_dump($this->GarageOrderSelected);
        return $this->renderHTML('/buys/garageOrdersForm.twig', [
            'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
            'responseMessage' => $responseMessage,
            'order' => $this->GarageOrderSelected,
            'vehicle' => $Vehicle,
            'customer' => $Customer
        ]);
    }

    public function deleteAction(ServerRequest $request)
    {         
        $this->GarageOrderService->deleteGarageOrder($request->getQueryParams('id'));               
        return new RedirectResponse('/intranet/buys/orders/list');
    }

   

}