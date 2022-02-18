<?php

namespace App\Controllers\Entitys;

use App\Controllers\BaseController;
use Respect\Validation\Validator as v;
use App\Models\Finance;
use App\Services\Entitys\FinanceService;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Response\RedirectResponse;

class FinanceController extends BaseController {     
    protected $financeService;
    public function __construct(FinanceService $financeService) {
        parent::__construct();
        $this->financeService = $financeService;
    }        
    public function getIndexAction() {
        $finances = $this->financeService->getAllRegisters(new Finance());
        return $this->renderHTML('/Entitys/finance/financeList.html.twig', [
            'finances' => $finances
        ]);
    }     
    public function getFinanceDataAction($request) {                
        $responseMessage = null;
        if($request->getMethod() == 'POST') {
            $postData = $request->getParsedBody();            
            $financeValidator = v::key('name', v::stringType()->notEmpty()) 
            ->key('fiscalId', v::notEmpty())
            ->key('phone', v::notEmpty())
            ->key('email', v::notEmpty());            
            try{
                $financeValidator->assert($postData); // true 
                $responseMessage = $this->financeService->saveRegister(new Finance(), $postData);   
            }catch(\Exception $e){                
                $responseMessage = $e->getMessage();
            }              
        }
        $financeSelected = $this->financeService->setInstance(new Finance(), $request->getQueryParams('id'));        
        return $this->renderHTML('/Entitys/finance/financeForm.html.twig', [
            'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
            'responseMessage' => $responseMessage,
            'finance' => $financeSelected
        ]);
    }
    public function deleteAction(ServerRequest $request) {         
        $this->financeService->deleteRegister(new Finance(), $request->getQueryParams('id'));               
        return new RedirectResponse('/Intranet/finance/list');
    }
}