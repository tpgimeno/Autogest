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
        $company = new CompanyClass();
        return $this->renderHTML('/Entitys/company/companyList.html.twig', [
            'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
            'companies' => $company->getAllRegisters()
        ]);
    }       
    public function searchCompanyAction($request) {
        $searchData = $request->getParsedBody();
        $searchString = $searchData['searchFilter']; 
        $company = new CompanyClass();
        $companies = $company->searchCompanies($searchString);
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
                $responseMessage = $this->saveCompanyData($postData);                   
            }catch(Exception $e){                
                $responseMessage = $e->getMessage();
            }              
        }
        $companySelected = null;
        $params = $request->getQueryParams();
        if($params && $this->findCompany($params)) {
            $companySelected = Company::find(intval($params['id']));
        }
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
    public function saveCompanyData($postData) {
        $company = new Company();   
        if($this->findCompany($postData)) {
            $company = Company::find(intval($postData['id']));            
        }                            
        $company->name = $postData['name'];
        $company->fiscalId = $postData['fiscalId'];
        $company->fiscalName = $postData['fiscalName'];
        $company->address = $postData['address'];
        $company->city = $postData['city'];
        $company->postalCode = $postData['postalCode'];
        $company->state = $postData['state'];
        $company->country = $postData['country'];
        $company->phone = $postData['phone'];
        $company->email = $postData['email'];
        $company->site = $postData['site'];
        if($this->findCompany($postData)) {
            $company->update();
            $responseMessage = 'Updated';
        }else{
            $company->save();     
            $responseMessage = 'Saved'; 
        } 
        return $responseMessage;
    }
    public function deleteAction(ServerRequest $request) {         
        $this->companyService->deleteCompany($request->getQueryParams('id'));               
        return new RedirectResponse('/Intranet/company/list');
    }
    
   

}