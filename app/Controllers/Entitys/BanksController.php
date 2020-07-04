<?php

namespace App\Controllers\Entitys;

use App\Controllers\BaseController;
use Respect\Validation\Validator as v;
use App\Models\Bank;
use App\Services\BankService;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Response\RedirectResponse;

class BanksController extends BaseController
{    
    
    protected $bankService;

    public function __construct(BankService $bankService)
    {
        parent::__construct();
        $this->bankService = $bankService;
    }    
    public function getIndexAction()
    {
        $banks = Bank::All();
        return $this->renderHTML('/banks/banksList.twig', [
            'banks' => $banks
        ]);
    }     
    public function getBankDataAction($request)
    {                
        $responseMessage = null;
        if($request->getMethod() == 'POST')
        {
            $postData = $request->getParsedBody();
            
            $bankValidator = v::key('name', v::stringType()->notEmpty()) 
            ->key('fiscal_id', v::notEmpty())
            ->key('phone', v::notEmpty())
            ->key('email', v::stringType()->notEmpty());            
            try{
                $bankValidator->assert($postData); // true 
                $bank = new Bank();
                $bank->name = $postData['name'];
                $bank->fiscal_id = $postData['fiscal_id'];
                $bank->fiscal_name = $postData['fiscal_name'];
                $bank->address = $postData['address'];
                $bank->city = $postData['city'];
                $bank->postal_code = $postData['postal_code'];
                $bank->state = $postData['state'];
                $bank->country = $postData['country'];
                $bank->phone = $postData['phone'];
                $bank->email = $postData['email'];
                $bank->site = $postData['site'];
                $bank->save();     
                $responseMessage = 'Saved';     
            }catch(\Exception $e){                
                $responseMessage = $e->getMessage();
            }              
        }
        $bankSelected = null;
        if($_GET)
        {
            $bankSelected = Bank::find($_GET['id']);
        }
        return $this->renderHTML('/banks/banksForm.twig', [
            'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
            'responseMessage' => $responseMessage,
            'bank' => $bankSelected
        ]);
    }
    public function deleteAction(ServerRequest $request)
    {
         
        $this->bankService->deleteBank($request->getQueryParams('id'));               
        return new RedirectResponse('/intranet/banks/list');
    }
}