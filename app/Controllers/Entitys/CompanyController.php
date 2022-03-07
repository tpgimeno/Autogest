<?php

namespace App\Controllers\Entitys;

use App\Controllers\BaseController;
use App\Models\Company;
use App\Services\Entitys\CompanyService;
use Exception;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Diactoros\ServerRequest;
use Respect\Validation\Validator as v;

class CompanyController extends BaseController {        
    protected $companyService;
    protected $list = '/Intranet/company/list';
    protected $tab = 'home';
    protected $title = 'Empresas';
    protected $save = "/Intranet/company/save";
    protected $formName = "CompanyForm";
    protected $search = "/Intranet/company/search";
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
    public function __construct(CompanyService $companyService) {
        parent::__construct();
        $this->companyService = $companyService;
    }    
    public function getIndexAction() {
        $companies = $this->companyService->getAllRegisters(new Company());
        return $this->renderHTML('/Entitys/company/companyList.html.twig', [
            'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
            'title' => $this->title,
            'tab' => $this->tab,
            'list' => $this->list,
            'companies' => $companies
        ]);
    }       
    public function searchCompanyAction($request) {
        $searchData = $request->getParsedBody();      
        $companies = $this->companyService->searchCompanies($searchData['searchFilter']);
        return $this->renderHTML('/Entitys/company/companyList.html.twig', [
            'currentUser' => $this->currentUser->getCurrentUserEmailAction(),
            'title' => $this->title,
            'tab' => $this->tab,
            'list' => $this->list,
            'companies' => $companies                
        ]);
    }    
    public function getCompanyDataAction($request) {                
        $responseMessage = null;
        if($request->getMethod() == 'POST') {
            $postData = $request->getParsedBody();            
            $companyValidator = v::key('name', v::stringType()->notEmpty()) 
            ->key('fiscalId', v::notEmpty())
            ->key('phone', v::notEmpty())
            ->key('email', v::stringType()->notEmpty());            
            try{
                $companyValidator->assert($postData); // true 
                $responseMessage = $this->companyService->saveRegister(new Company(), $postData);                   
            }catch(Exception $e){                
                $responseMessage = $e->getMessage();
            }              
        }        
        $companySelected = $this->companyService->setInstance(new Company(), $request->getQueryParams());
        return $this->renderHTML('/Entitys/company/companyForm.html.twig', [
            'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
            'responseMessage' => $responseMessage,
            'inputs' => $this->inputs,
            'save' => $this->save,
            'list' => $this->list,
            'formName' => $this->formName,
            'tab' => $this->tab,
            'title' => $this->title,
            'value' => $companySelected
        ]);
    }    
    public function deleteAction(ServerRequest $request) {         
        $this->companyService->deleteRegister(new Company(), $request->getQueryParams('id'));               
        return new RedirectResponse('/Intranet/company/list');
    }
    
   

}