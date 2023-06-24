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
        $this->model = new Company();
        $this->route = 'company';
        $this->titleList = 'Empresas';
        $this->titleForm = 'Empresa';
        $this->labels = $this->companyService->getLabelsArray(); 
        $this->itemsList = array('id', 'name', 'email', 'access', 'phone');
    }

    public function getIndexAction($request) {
        return $this->getBaseIndexAction($request, $this->model);
    }

    public function getCompanyDataAction($request) {
        $responseMessage = null;         
        if ($request->getMethod() == 'POST') {
            $postData = $request->getParsedBody();
            $companyValidator = v::key('name', v::stringType()->notEmpty())
                    ->key('fiscalId', v::notEmpty())
                    ->key('phone', v::notEmpty())
                    ->key('email', v::stringType()->notEmpty());
            try {
                $companyValidator->assert($postData); // true
            } catch (Exception $e) {
                $responseMessage = $this->errorService->getError($e);
            }
            return $this->getBasePostDataAction($request, $this->model, null, $responseMessage);
        }else{        
            return $this->getBaseGetDataAction($request, $this->model, null);
        }          
    }

    public function deleteAction(ServerRequest $request) { 
        $params = $request->getQueryParams();        
        $this->companyService->deleteRegister($this->model, $params);
        return new RedirectResponse('/Intranet/company/list?menu=' . $params['menu'] . '&item=' . $params['item']);
    }

}
