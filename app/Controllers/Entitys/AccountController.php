<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Controllers\Entitys;

use App\Controllers\BaseController;
use App\Models\Accounts;
use App\Services\Entitys\AccountService;
use Exception;
use Respect\Validation\Validator as v;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Diactoros\ServerRequest;

/**
 * Description of AccountController
 *
 * @author tonyl
 */
class AccountController extends BaseController {
    protected $accountService;
    protected $list = '/Intranet/accounts/list';
    protected $tab = 'home';
    protected $title = 'Cuentas Bancarias';
    protected $save = "/Intranet/accounts/save";
    protected $formName = "accountsForm";
    protected $inputs = ['id' => ['id' => 'inputID', 'name' => 'id', 'title' => 'ID'],
        'selectBank' => ['id' => 'selectBank', 'name' => 'bank', 'title' => 'Banco'],
        'selectOwner' => ['id' => 'selectOwner', 'name' => 'owner', 'title' => 'Titular'],
        'observations' => ['id' => 'observations', 'name' => 'observations', 'title' => 'Observaciones'],
        'accountNumber' => ['id' => 'inputAccountNumber', 'name' => 'accountNumber', 'title' => 'Numero de Cuenta']];
    public function __construct(AccountService $accountService) {
        parent::__construct();
        $this->accountService = $accountService;
    }    
    public function getIndexAction() {
        $accounts = $this->accountService->getAllAccounts();       
        return $this->renderHTML('/Entitys/accounts/accountsList.html.twig', [
            'list' => $this->list,
            'tab' => $this->tab,
            'title' => $this->title,
            'accounts' => $accounts
        ]);
    }  
    public function getAccountDataAction($request) {                
        $responseMessage = null;
        if($request->getMethod() == 'POST') {
            $postData = $request->getParsedBody();            
            $accountValidator = v::key('bank', v::notEmpty()) 
            ->key('owner', v::notEmpty())
            ->key('accountNumber', v::notEmpty());                      
            try{
                $accountValidator->assert($postData); // true 
                $postData['bank'] = $this->accountService->getBankByName($postData);
                $postData['owner'] = $this->accountService->getOwnerByName($postData);
                $responseMessage = $this->accountService->saveRegister(new Accounts(), $postData);                   
            }catch(Exception $e) {                
                $responseMessage = $e->getMessage();
            }              
        }
        $accountSelected = $this->accountService->setAccountData($request->getQueryParams('id'));
        $banks = $this->accountService->getBankNames();
        $owners = $this->accountService->getOwnerNames();
        return $this->renderHTML('/Entitys/accounts/accountsForm.html.twig', [
            'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
            'responseMessage' => $responseMessage,
            'value' => $accountSelected,
            'banks' => $banks,
            'owners' => $owners,
            'inputs' => $this->inputs,
            'save' => $this->save,
            'list' => $this->list,
            'formName' => $this->formName,
            'tab' => $this->tab,
            'title' => $this->title
        ]);
    }
    public function deleteAction(ServerRequest $request) {         
        $this->accountService->deleteRegister(new Accounts(), $request->getQueryParams('id'));               
        return new RedirectResponse('/Intranet/accounts/list');
    }
}
