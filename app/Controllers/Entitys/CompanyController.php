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

    public function __construct(CompanyService $companyService) {
        parent::__construct();
        $this->companyService = $companyService;
    }

    public function getIndexAction() {
        $companies = $this->companyService->getAllRegisters(new Company());
        return $this->renderHTML('/Entitys/company/companyList.html.twig', [
                    'companies' => $companies
        ]);
    }

    public function getCompanyDataAction($request) {
        $responseMessage = null;
        $companySelected = null;
        if ($request->getMethod() == 'POST') {
            $postData = $request->getParsedBody();
            $companyValidator = v::key('name', v::stringType()->notEmpty())
                    ->key('fiscalId', v::notEmpty())
                    ->key('phone', v::notEmpty())
                    ->key('email', v::stringType()->notEmpty());
            try {
                $companyValidator->assert($postData); // true                 
                $response = $this->companyService->saveRegister(new Company(), $postData);                
                $companySelected = $this->companyService->findCompany(array('id' => $response[0]));
                $responseMessage = $response[1];
            } catch (Exception $e) {
                $responseMessage = $e->getMessage();
            }
        }else{        
            $companySelected = $this->companyService->setInstance(new Company(), $request->getQueryParams()); 
        }
        return $this->renderHTML('/Entitys/company/companyForm.html.twig', [
                    'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
                    'responseMessage' => $responseMessage,                    
                    'companySelected' => $companySelected
        ]);
    }

    public function deleteAction(ServerRequest $request) {        
        $this->companyService->deleteRegister(new Company(), $request->getQueryParams('id'));
        return new RedirectResponse('/Intranet/company/list');
    }

}
