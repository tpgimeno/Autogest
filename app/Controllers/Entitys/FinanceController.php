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
    protected $list = '/Intranet/finance/list';
    protected $tab = 'home';
    protected $title = 'Financieras';
    protected $save = "/Intranet/finance/save";
    protected $formName = "financeForm";
    protected $inputs = ['id' => ['id' => 'inputID', 'name' => 'id', 'title' => 'ID'],
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
    public function __construct(FinanceService $financeService) {
        parent::__construct();
        $this->financeService = $financeService;
    }        
    public function getIndexAction() {
        $finances = $this->financeService->getAllRegisters(new Finance());
        return $this->renderHTML('/Entitys/finance/financeList.html.twig', [
            'list' => $this->list,
            'tab' => $this->tab,
            'title' => $this->title,
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
            'inputs' => $this->inputs,
            'save' => $this->save,
            'list' => $this->list,
            'formName' => $this->formName,
            'tab' => $this->tab,
            'title' => $this->title,
            'value' => $financeSelected
        ]);
    }
    public function deleteAction(ServerRequest $request) {         
        $this->financeService->deleteRegister(new Finance(), $request->getQueryParams('id'));               
        return new RedirectResponse($this->list);
    }
}