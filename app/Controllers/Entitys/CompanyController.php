<?php

namespace App\Controllers\Entitys;

use App\Controllers\BaseController;
use Respect\Validation\Validator as v;
use App\Models\Company;
use App\Services\CompanyService;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Response\RedirectResponse;

class CompanyController extends BaseController
{    
    
    protected $companyService;

    public function __construct(CompanyService $companyService)
    {
        parent::__construct();
        $this->companyService = $companyService;
    }
    
    
    
    public function getIndexAction()
    {
        $company = Company::All();
        return $this->renderHTML('/company/companyList.twig', [
            'company' => $company
        ]);
    }   
    
    public function getCompanyDataAction($request)
    {                
        $responseMessage = null;
        if($request->getMethod() == 'POST')
        {
            $postData = $request->getParsedBody();
            
            $companyValidator = v::key('name', v::stringType()->notEmpty()) 
            ->key('fiscal_id', v::notEmpty())
            ->key('phone', v::notEmpty())
            ->key('email', v::stringType()->notEmpty());            
            try{
                $companyValidator->assert($postData); // true 
                $company = new Company();
                $company->name = $postData['name'];
                $company->fiscal_id = $postData['fiscal_id'];
                $company->fiscal_name = $postData['fiscal_name'];
                $company->address = $postData['address'];
                $company->city = $postData['city'];
                $company->postal_code = $postData['postal_code'];
                $company->state = $postData['state'];
                $company->country = $postData['country'];
                $company->phone = $postData['phone'];
                $company->email = $postData['email'];
                $company->site = $postData['site'];
                $company->save();     
                $responseMessage = 'Saved';     
            }catch(\Exception $e){                
                $responseMessage = $e->getMessage();
            }              
        }
        $companySelected = null;
        if($_GET)
        {
            $companySelected = Company::find($_GET['id']);
        }
        return $this->renderHTML('/company/companyForm.twig', [
            'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
            'responseMessage' => $responseMessage,
            'company' => $companySelected
        ]);
    }

    public function deleteAction(ServerRequest $request)
    {
         
        $this->companyService->deleteCompany($request->getQueryParams('id'));               
        return new RedirectResponse('/intranet/company/list');
    }

   

}