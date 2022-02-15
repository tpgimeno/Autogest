<?php

namespace App\Controllers\Entitys;

use App\BackEnd\Classes\CompanyClass;
use App\Controllers\BaseController;
use App\Models\Company;
use App\Services\Entitys\CompanyService;
use Exception;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Diactoros\ServerRequest;
use Respect\Validation\Validator as v;

class CompanyController extends BaseController {        
    protected $companyService;
    public function __construct(CompanyService $companyService) {
        parent::__construct();
        $this->companyService = $companyService;
    }    
    public function getIndexAction() {
        $companies = $this->companyService->getAllRegisters();
        return $this->renderHTML('/Entitys/company/companyList.html.twig', [
            'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
            'companies' => $companies
        ]);
    }       
    public function searchCompanyAction($request) {
        $searchData = $request->getParsedBody();      
        $companies = $this->companyService->searchCompanies($searchData['searchFilter']);
        return $this->renderHTML('/Entitys/company/companyList.html.twig', [
            'currentUser' => $this->currentUser->getCurrentUserEmailAction(),
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
                $responseMessage = $this->companyService->saveCompanyData($postData);                   
            }catch(Exception $e){                
                $responseMessage = $e->getMessage();
            }              
        }        
        $params = $request->getQueryParams();
        $companySelected = $this->companyService->findCompany($params);
        return $this->renderHTML('/Entitys/company/companyForm.html.twig', [
            'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
            'responseMessage' => $responseMessage,
            'company' => $companySelected
        ]);
    }
    public function findCompany($postData){
        $company = null;
        if(isset($postData['id']) && $postData['id']){
            $company = Company::find(intval($postData['id']));
        }
        if($company){
            return true;
        }else{
            return false;
        }
    }
    
    public function deleteAction(ServerRequest $request) {         
        $this->companyService->deleteCompany($request->getQueryParams('id'));               
        return new RedirectResponse('/Intranet/company/list');
    }
    
   

}