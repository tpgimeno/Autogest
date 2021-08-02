<?php

namespace App\Controllers\Entitys;

use App\Controllers\BaseController;
use Respect\Validation\Validator as v;
use App\Models\Finance;
use App\Services\FinanceService;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Response\RedirectResponse;

class FinanceController extends BaseController
{    
    
    protected $financeService;

    public function __construct(FinanceService $financeService)
    {
        parent::__construct();
        $this->financeService = $financeService;
    }    
    
    public function getIndexAction()
    {
        $finances = Finance::All();
        return $this->renderHTML('/finance/financeList.html.twig', [
            'finances' => $finances
        ]);
    }   
    
    public function getFinanceDataAction($request)
    {                
        $responseMessage = null;
        if($request->getMethod() == 'POST')
        {
            $postData = $request->getParsedBody();
            
            $financeValidator = v::key('name', v::stringType()->notEmpty()) 
            ->key('fiscal_id', v::notEmpty())
            ->key('phone', v::notEmpty())
            ->key('email', v::stringType()->notEmpty());            
            try{
                $financeValidator->assert($postData); // true 
                $finance = new Finance();
                $finance->name = $postData['name'];
                $finance->fiscal_id = $postData['fiscal_id'];
                $finance->fiscal_name = $postData['fiscal_name'];
                $finance->address = $postData['address'];
                $finance->city = $postData['city'];
                $finance->postal_code = $postData['postal_code'];
                $finance->state = $postData['state'];
                $finance->country = $postData['country'];
                $finance->phone = $postData['phone'];
                $finance->email = $postData['email'];
                $finance->site = $postData['site'];
                $finance->save();     
                $responseMessage = 'Saved';     
            }catch(\Exception $e){                
                $responseMessage = $e->getMessage();
            }              
        }
        $financeSelected = null;
        if($request->getQueryParams('id'))
        {
            $financeSelected = Finance::find($request->getQueryParams('id'))->first();
        }
        return $this->renderHTML('/finance/financeForm.html.twig', [
            'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
            'responseMessage' => $responseMessage,
            'finance' => $financeSelected
        ]);
    }

    public function deleteAction(ServerRequest $request)
    {
         
        $this->financeService->deleteFinance($request->getQueryParams('id'));               
        return new RedirectResponse('/Intranet/finance/list');
    }

   

}