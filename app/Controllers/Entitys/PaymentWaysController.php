<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Controllers\Entitys;

use App\Controllers\BaseController;
use App\Models\Accounts;
use App\Models\PaymentWays;
use App\Services\Entitys\PaymentWaysService;
use Exception;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Diactoros\ServerRequest;
use Respect\Validation\Validator as v;
/**
 * Description of paymentWayController
 *
 * @author TpGimeno
 */
class PaymentWaysController extends BaseController {
    protected $paymentWayService;
    public function __construct(PaymentWaysService $paymentWayService) {
        parent::__construct();
        $this->paymentWayService = $paymentWayService;
    }
    public function getIndexAction() {
        $paymentWays = $this->paymentWayService->getAllRegisters(new PaymentWays());
        $accounts = $this->paymentWayService->getAllRegisters(new Accounts());
        return $this->renderHTML('/Entitys/paymentWays/paymentWaysList.html.twig', [
            'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
            'paymentWays' => $paymentWays,
            'accounts' => $accounts
        ]);
    }
    public function searchPaymentWayAction($request) {
        $searchData = $request->getParsedBody();        
        $searchString = $searchData['searchFilter']; 
        $paymentWays = $this->paymentWayService->searchPaymentWay($searchString);
        $accounts = $this->paymentWayService->getAllRegisters(new Accounts());
        return $this->renderHTML('/Entitys/paymentWays/paymentWaysList.html.twig', [
            'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
            'paymentWays' => $paymentWays,
            'accounts' => $accounts
        ]);
    }   
    public function getPaymentWaysDataAction($request) {        
        $responseMessage = null;
        if($request->getMethod() == 'POST'){
            $postData = $request->getParsedBody();            
            try{
                $paymentWayValidator = v::key('name', v::stringType()->notEmpty());
                $paymentWayValidator->assert($postData); // true 
                $postData['account'] = $this->paymentWayService->getAccountByNumber($postData);
                $responseMessage = $this->paymentWayService->saveRegister(new PaymentWays(), $postData);
            } catch (Exception $ex) {
                $responseMessage = $ex->getMessage();
            }          
        }        
        $paymentWaySelected = $this->paymentWayService->setInstance(new PaymentWays(), $request->getQueryParams('id'));
        $accounts = $this->paymentWayService->getAllRegisters(new Accounts());
        return $this->renderHTML('/Entitys/paymentWays/paymentWaysForm.html.twig', [
        'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
        'responseMessage' => $responseMessage,
        'paymentWay' => $paymentWaySelected,
        'accounts' => $accounts
        ]);               
    }    
    public function deleteAction(ServerRequest $request) {       
        $this->paymentWayService->deleteRegister(new PaymentWays(), $request->getQueryParams('id'));           
        return new RedirectResponse('/Intranet/paymentWays/list');
    }
}

