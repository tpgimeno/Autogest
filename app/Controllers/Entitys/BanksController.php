<?php

namespace App\Controllers\Entitys;

use App\Controllers\BaseController;
use App\Models\Bank;
use App\Services\Entitys\BankService;
use Exception;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Diactoros\ServerRequest;
use Respect\Validation\Validator as v;

class BanksController extends BaseController {

    protected $bankService;

    public function __construct(BankService $bankService) {
        parent::__construct();
        $this->bankService = $bankService;
        $this->model = new Bank();
        $this->route = 'banks';
        $this->titleList = 'Bancos';
        $this->titleForm = 'Banco';
        $this->labels = $this->bankService->getLabelsArray(); 
        $this->itemsList = array('id', 'bankCode', 'name', 'email', 'phone');
        $this->properties = $this->bankService->getModelProperties($this->model);
        
    }

    public function getIndexAction($request) {
        return $this->getBaseIndexAction($request, $this->model, null);
    }

    public function getBankDataAction($request) {  
        $responseMessage = null;
        if ($request->getMethod() == 'POST') {
            $postData = $request->getParsedBody();
            $bankValidator = v::key('name', v::stringType()->notEmpty())
                    ->key('fiscalId', v::notEmpty())
                    ->key('phone', v::notEmpty())
                    ->key('email', v::notEmpty());
            try {
                $bankValidator->assert($postData); // true                 
            } catch (Exception $e) {
                $responseMessage = $this->errorService->getError($e);
            }
            return $this->getBasePostDataAction($request, $this->model, null, $responseMessage);
        } else {
            return $this->getBaseGetDataAction($request, $this->model, null);
        }
    }

    public function deleteAction(ServerRequest $request) {
        $params = $request->getQueryParams();            
        $this->bankService->deleteRegister(new Bank(), $params);
        return new RedirectResponse('/Intranet/banks/list?menu=' . $params['menu'] . '&item=' . $params['item']);
    }

}
