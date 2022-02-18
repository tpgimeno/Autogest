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
    public function __construct(BankService $bankService) {
        parent::__construct();
        $this->bankService = $bankService;
    }    
    public function getIndexAction() {
        $banks = $this->bankService->getAllRegisters(new Bank());
        return $this->renderHTML('/Entitys/banks/banksList.html.twig', [
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
            try{
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
            'bank' => $bankSelected
        ]);
    }
    public function deleteAction(ServerRequest $request) {         
        $this->bankService->deleteRegister(new Bank(), $request->getQueryParams('id'));               
        return new RedirectResponse('/Intranet/banks/list');
    }
}