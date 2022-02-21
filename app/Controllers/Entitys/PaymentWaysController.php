<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Controllers\Entitys;

use App\Controllers\BaseController;
use App\Models\PaymentWays;
use App\Services\Entitys\PaymentWaysService;
use Exception;
use Illuminate\Database\Capsule\Manager as DB;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Diactoros\ServerRequest;
use Respect\Validation\Validator as v;
/**
 * Description of paymentWayController
 *
 * @author TpGimeno
 */
class PaymentWaysController extends BaseController
{
    protected $paymentWayService;
    public function __construct(PaymentWaysService $paymentWayService) {
        parent::__construct();
        $this->paymentWayService = $paymentWayService;
    }
    public function getIndexAction()
    {
        $paymentWays = PaymentWays::all();        
        return $this->renderHTML('/Entitys/paymentWays/paymentWaysList.html.twig', [
            'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
            'paymentWays' => $paymentWays            
        ]);
    }
    public function searchPaymentWayAction($request)
    {
        $searchData = $request->getParsedBody();        
        $searchString = $searchData['searchFilter']; 
        if($searchString)
        {
            $paymentWays = DB::table('paymentWays')                            
                ->where('paymentWays.name', 'like', "%".$searchString."%")
                ->orWhere('paymentWays.percentaje', 'like', "%".$searchString."%") 
                ->whereNull('paymentWays.deleted_at')
                ->get();
        }
        else
        {
             $paymentWays = DB::table('paymentWays')                               
                ->select('paymentWays.id', 'paymentWays.name', 'paymentWays.percentaje')
                ->whereNull('paymentWays.deleted_at')
                ->get();
        }        
        return $this->renderHTML('/Entitys/paymentWays/paymentWaysList.html.twig', [
            'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
            'paymentWays' => $paymentWays                    
        ]);
    }
    public function validateData($postData)
    {
        $responseMessage = null;           
        $paymentWayValidator = v::key('name', v::stringType()->notEmpty());        
        try{
            $paymentWayValidator->assert($postData); // true 
        }catch(Exception $e){                
            $responseMessage = $e->getMessage();
        }
        return $responseMessage;
    }
    public function getPaymentWaysDataAction($request)    {        
        $responseMessage = null;
        if($request->getMethod() == 'POST'){
            $postData = $request->getParsedBody();
            if($postData) {
                $responseMessage = $this->validateData($postData);
            }            
            if($postData){
                $responseMessage = $this->saveAction($postData);
            }
        }        
        $paymentWaySelected = $this->renderSelected($request);
        return $this->renderHTML('/Entitys/paymentWays/paymentWaysForm.html.twig', [
        'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
        'responseMessage' => $responseMessage,
        'paymentWay' => $paymentWaySelected
        ]);
               
    }
    public function renderSelected($request) {
        $paymentWaySelected = null;
        if($request->getQueryParams('id')){
            $paymentWaySelected = PaymentWays::find($request->getQueryParams('id'))->first();
        }        
        return $paymentWaySelected;        
    }
    public function saveAction($postData) {        
        try{
                $paymentWay = new PaymentWays();
                $paymentWay->name = $postData['name'];               
                $paymentWay->discount = $postData['discount'];
                $paymentWay->save();     
                $Message = 'Saved';
        }catch(Exception $e){                
                $Message = $e->getMessage();
        }
        return $Message;
    }
    public function deleteAction(ServerRequest $request){       
        $this->paymentWayService->deletepaymentWay($request->getQueryParams('id'));               
        return new RedirectResponse('/Intranet/paymentWays/list');
    }

}

