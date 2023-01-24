<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Controllers\Entitys;

use App\Controllers\BaseController;
use App\Models\Accounts;
use App\Models\Bank;
use App\Services\Entitys\AccountService;
use Exception;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Diactoros\ServerRequest;
use Respect\Validation\Validator as v;

/**
 * Description of AccountController
 *
 * @author tonyl
 */
class AccountController extends BaseController {
    protected $accountService;
    
    public function __construct(AccountService $accountService) {
        parent::__construct();
        $this->accountService = $accountService;
        $this->model = new Accounts();
        $this->route = 'accounts';
        $this->titleList = 'Cuentas Bancarias';
        $this->titleForm = 'Cuenta Bancaria';
        $this->labels = $this->accountService->getLabelsArray(); 
        $this->itemsList = array('id', 'bank', 'accountNumber', 'owner');
    }    
    public function getIndexAction($request) {
        return $this->getBaseIndexAction($request, $this->model);
    }  
    public function getAccountDataAction($request) {                
        $responseMessage = null;
        $banks = $this->accountService->getAllRegisters(new Bank());
        $iterables = ['bank' => $banks];
        if($request->getMethod() == 'POST') {
            $postData = $request->getParsedBody();            
            $accountValidator = v::key('bank', v::notEmpty()) 
            ->key('owner', v::notEmpty())
            ->key('accountNumber', v::notEmpty());                      
            try{
                $accountValidator->assert($postData); // true                                    
            }catch(Exception $e) {                
                $responseMessage = $e->getMessage();
            } 
            return $this->getBasePostDataAction($request, $this->model, $iterables, $responseMessage);
        }else{
            return $this->getBaseGetDataAction($request, $this->model, $iterables);
        }
    }
    public function deleteAction(ServerRequest $request) {         
        $this->accountService->deleteRegister(new Accounts(), $request->getQueryParams('id'));               
        return new RedirectResponse('/Intranet/accounts/list');
    }
}
