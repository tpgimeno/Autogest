<?php

namespace App\Controllers\Entitys;

use App\Controllers\BaseController;
use Respect\Validation\Validator as v;
use App\Models\Bank;
use App\Services\Entitys\BankService;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Response\RedirectResponse;

class BanksController extends BaseController {       
    protected $bankService;
    protected $list = '/Intranet/banks/list';
    protected $tab = 'home';
    protected $title = 'Bancos';
    protected $save = "/Intranet/banks/save";
    protected $formName = "banksForm";
    protected $inputs = ['id' => ['id' => 'inputID', 'name' => 'id', 'title' => 'ID'],
        'bankCode' => ['id' => 'inputBankCode', 'name' => 'bankCode', 'title' => 'CODE'],
        'name' => ['id' => 'inputName', 'name' => 'name', 'title' => 'Nombre'],
        'fiscalId' => ['id' => 'inputFiscalId', 'name' => 'fiscalId', 'title' => 'NIF/CIF'],
        'fiscalName' => ['id' => 'inputSocialName', 'name' => 'fiscalName', 'title' => 'Razón Social'],
        'address' => ['id' => 'inputAdress', 'name' => 'address', 'title' => 'Dirección'],
        'postalCode' => ['id' => 'inputZip', 'name' => 'postalCode', 'title' => 'Código Postal'],
        'city' => ['id' => 'inputCity', 'name' => 'city', 'title' => 'Población'],
        'state' => ['id' => 'inputState', 'name' => 'state', 'title' => 'Provincia'],
        'country' => ['id' => 'inputCountry', 'name' => 'country', 'title' => 'Pais'],
        'phone' => ['id' => 'inputPhone', 'name' => 'phone', 'title' => 'Telefono'],
        'email' => ['id' => 'inputEmail', 'name' => 'email', 'title' => 'Email'],
        'site' => ['id' => 'inputWeb', 'name' => 'site', 'title' => 'Página Web']];
    public function __construct(BankService $bankService) {
        parent::__construct();
        $this->bankService = $bankService;
    }    
    public function getIndexAction() {
        $banks = $this->bankService->getAllRegisters(new Bank());
        return $this->renderHTML('/Entitys/banks/banksList.html.twig', [
            'list' => $this->list,
            'title' => $this->title,
            'banks' => $banks
        ]);
    }  
    public function getBankDataAction($request) {                
        $responseMessage = null;
        if($request->getMethod() == 'POST') {
            $postData = $request->getParsedBody();            
            $bankValidator = v::key('name', v::stringType()->notEmpty()) 
            ->key('fiscalId', v::notEmpty())
            ->key('phone', v::notEmpty())
            ->key('email', v::notEmpty());            
            try {
                $bankValidator->assert($postData); // true 
                $responseMessage = $this->bankService->saveRegister(new Bank(), $postData);                   
            }catch(\Exception $e) {                
                $responseMessage = $e->getMessage();
            }              
        }      
        $bankSelected = $this->bankService->setInstance(new Bank(), $request->getQueryParams('id'));           
        return $this->renderHTML('/Entitys/banks/banksForm.html.twig', [
            'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
            'responseMessage' => $responseMessage,
            'inputs' => $this->inputs,
            'save' => $this->save,
            'list' => $this->list,
            'formName' => $this->formName,
            'tab' => $this->tab,
            'title' => $this->title,
            'value' => $bankSelected
        ]);
    }
    public function deleteAction(ServerRequest $request) {         
        $this->bankService->deleteRegister(new Bank(), $request->getQueryParams('id'));               
        return new RedirectResponse($this->list);
    }
}