<?php

namespace App\Controllers\Entitys;

use App\Controllers\BaseController;
use App\Models\Bank;
use App\Models\Finance;
use App\Services\Entitys\FinanceService;
use Exception;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Diactoros\ServerRequest;
use Respect\Validation\Validator as v;

class FinanceController extends BaseController {

    protected $financeService;

    public function __construct(FinanceService $financeService) {
        parent::__construct();
        $this->financeService = $financeService;
        $this->model = new Finance();
        $this->route = 'finance';
        $this->titleList = 'Financieras';
        $this->titleForm = 'Financiera';
        $this->labels = $this->financeService->getLabelsArray();
        $this->itemsList = array('id', 'bank', 'name', 'email', 'phone');
        $this->properties = $this->financeService->getModelProperties($this->model);
    }

    public function getIndexAction($request) {
        return $this->getBaseIndexAction($request, $this->model);
    }

    public function getFinanceDataAction($request) {
        $responseMessage = null;
        $banks = $this->financeService->getAllRegisters(new Bank());
        $iterables = ['bankId' => $banks];
        if ($request->getMethod() == 'POST') {
            $postData = $request->getParsedBody();
            $financeValidator = v::key('name', v::stringType()->notEmpty())
                    ->key('fiscalId', v::notEmpty())
                    ->key('phone', v::notEmpty())
                    ->key('email', v::notEmpty());
            try {
                $financeValidator->assert($postData); // true                 
            } catch (Exception $e) {
                $responseMessage = $e->getMessage();
            }
            return $this->getBasePostDataAction($request, $this->model, $iterables, $responseMessage);
        } else {           
            return $this->getBaseGetDataAction($request, $this->model, $iterables);
        }
    }

    public function deleteAction(ServerRequest $request) {
        $params = $request->getQueryParams();
        $this->financeService->deleteRegister($this->model, $params);
        return new RedirectResponse('/Intranet/finance/list?menu=mantenimiento&item=finance');
    }

}
