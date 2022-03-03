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
    protected $list = '/Intranet/paymentWays/list';
    protected $tab = 'buys';
    protected $title = 'Formas de Pago';
    protected $save = "/Intranet/paymentWays/save";
    protected $formName = "paymentWaysForm";
    protected $search = "/Intranet/paymentWays/search";
    protected $inputs = ['id' => ['id' => 'inputID', 'name' => 'id', 'title' => 'ID'],
        'name' => ['id' => 'inputName', 'name' => 'name', 'title' => 'Nombre'],
        'selectAccount' => ['id' => 'selectAccount', 'name' => 'account', 'title' => 'Cuenta Asociada'],
        'discount' => ['id' => 'inputDiscount', 'name' => 'discount', 'title' => 'Descuento']];
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
            'list' => $this->list,
            'tab' => $this->tab,
            'search' => $this->search,
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
            'list' => $this->list,
            'tab' => $this->tab,
            'title' => $this->title,
            'save' => $this->save,
            'formName' => $this->formName,
            'search' => $this->search,
            'inputs' => $this->inputs,
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
        $paymentWaySelected = $this->paymentWayService->setPaymentWay($request->getQueryParams('id'));
        $accounts = $this->paymentWayService->getAccounts();
        return $this->renderHTML('/Entitys/paymentWays/paymentWaysForm.html.twig', [
        'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
        'responseMessage' => $responseMessage,
        'value' => $paymentWaySelected,
        'list' => $this->list,
        'tab' => $this->tab,
        'title' => $this->title,
        'save' => $this->save,
        'formName' => $this->formName,
        'search' => $this->search,
        'inputs' => $this->inputs,
        'accounts' => $accounts
        ]);               
    }    
    public function deleteAction(ServerRequest $request) {       
        $this->paymentWayService->deleteRegister(new PaymentWays(), $request->getQueryParams('id'));           
        return new RedirectResponse('/Intranet/paymentWays/list');
    }
}

