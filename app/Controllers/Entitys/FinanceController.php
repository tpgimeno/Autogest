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
        $this->route = 'finance';
        $this->titleList = 'Financieras';
        $this->titleForm = 'Financiera';
        $this->labels = $this->financeService->getLabelsArray();
        $this->itemsList = array('id', 'bank', 'name', 'email', 'phone');
        $this->properties = $this->financeService->getModelProperties(new Finance());
    }

    public function getIndexAction($request) {
        $params = $request->getQueryParams();
        $menuState = $params['menu'];
        $menuItem = $params['item'];
        $finances = $this->financeService->getAllRegisters(new Finance());
        return $this->renderHTML('templateListView.html.twig', [
                    'route' => $this->route,
                    'titleList' => $this->titleList,
                    'labels' => $this->labels,
                    'itemsList' => $this->itemsList,
                    'menuState' => $menuState,
                    'menuItem' => $menuItem,
                    'values' => $finances
        ]);
    }

    public function getFinanceDataAction($request) {
        $responseMessage = null;
        $financeSelected = null;
        if ($request->getMethod() == 'POST') {
            $postData = $request->getParsedBody();
            $financeValidator = v::key('name', v::stringType()->notEmpty())
                    ->key('fiscalId', v::notEmpty())
                    ->key('phone', v::notEmpty())
                    ->key('email', v::notEmpty());
            try {
                $financeValidator->assert($postData); // true 
                $menuState = $postData['menu'];
                $menuItem = $postData['menuItem'];
                $responseMessage = $this->financeService->saveRegister(new Finance(), $postData);
                $financeSelected = $this->financeService->setInstance(new Finance(), $postData);
            } catch (Exception $e) {
                $responseMessage = $e->getMessage();
            }
        } else {
            $params = $request->getQueryParams();
            $menuState = $params['menu'];
            $menuItem = $params['item'];
            $financeSelected = $this->financeService->setInstance(new Finance(), $params);
        }
        $banks = $this->financeService->getAllRegisters(new Bank());
        $iterables = ['bankId' => $banks];
        return $this->renderHTML('templateFormView.html.twig', [
                    'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
                    'responseMessage' => $responseMessage,
                    'menuState' => $menuState,
                    'menuItem' => $menuItem,
                    'route' => $this->route,
                    'properties' => $this->properties,
                    'labels' => $this->labels,
                    'titleForm' => $this->titleForm,
                    'value' => $financeSelected,
                    'optionsArray' => $iterables              
        ]);
    }

    public function deleteAction(ServerRequest $request) {
        $params = $request->getQueryParams();
        $this->financeService->deleteRegister(new Finance(), $params);
        return new RedirectResponse('/Intranet/finance/list?menu=mantenimiento&item=finance');
    }

}
