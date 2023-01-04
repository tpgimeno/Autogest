<?php

namespace App\Controllers\Entitys;

use App\Controllers\BaseController;
use App\Models\Company;
use App\Models\Label;
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
        $this->route = 'company';
        $this->titleList = 'Empresas';
        $this->titleForm = 'Empresa';
        $this->labels = $this->companyService->getLabelsArray(); 
        $this->itemsList = array('id', 'name', 'email', 'access', 'phone');
    }

    public function getIndexAction($request) {
        $params = $request->getQueryParams();
        
        $menuState = $params['menu'];  
        $menuItem = $params['item'];
        $companies = $this->companyService->getAllRegisters(new Company());
        return $this->renderHTML('/templateListView.html.twig', [
                    'values' => $companies,
                    'route' => $this->route,
                    'title' => $this->titleList,
                    'itemsList' => $this->itemsList,
                    'labels' => $this->labels,                    
                    'menuState' => $menuState,
                    'menuItem' => $menuItem
        ]);
    }

    public function getCompanyDataAction($request) {
        $responseMessage = null;
        $companySelected = null;
        $menuState = null;
        $menuItem = null;
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
                $menuState = $postData['menu'];
                $menuItem = $postData['menuItem'];
                $this->properties = $this->companyService->getModelProperties();   
                $responseMessage = $response[1];
            } catch (Exception $e) {
                $responseMessage = $this->errorService->getError($e);
            }
        }else{        
            $params = $request->getQueryParams();            
            $companySelected = $this->companyService->setInstance(new Company(), $params);            
            $menuState = $params['menu'];
            $menuItem = $params['item'];
            $this->properties = $this->companyService->getModelProperties();  
        }        
        return $this->renderHTML('templateFormView.html.twig', [
                    'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
                    'responseMessage' => $responseMessage,                    
                    'value' => $companySelected,
                    'labels' => $this->labels,
                    'menuState' => $menuState,
                    'menuItem' => $menuItem,
                    'properties' => $this->properties,
                    'title' => $this->titleForm,
                    'route' => $this->route                    
        ]);
    }

    public function deleteAction(ServerRequest $request) { 
        $params = $request->getQueryParams();
        $this->companyService->deleteRegister(new Company(), $params['id']);
        return new RedirectResponse('/Intranet/company/list?menu=' . $params['menu'] . '&item=' . $params['item']);
    }

}
