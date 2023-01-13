<?php

namespace App\Controllers\Entitys;

use App\Controllers\BaseController;
use Respect\Validation\Validator as v;
use App\Models\Bank;
use App\Services\Entitys\BankService;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Response\RedirectResponse;

class BanksController extends BaseController {

    protected $bankService;

    public function __construct(BankService $bankService) {
        parent::__construct();
        $this->bankService = $bankService;
        $this->route = 'banks';
        $this->titleList = 'Bancos';
        $this->titleForm = 'Banco';
        $this->labels = $this->bankService->getLabelsArray(); 
        $this->itemsList = array('id', 'bankCode', 'name', 'email', 'phone');
        $this->properties = $this->bankService->getModelProperties(new Bank());
        
    }

    public function getIndexAction($request) {
        $params = $request->getQueryParams();
        $menuState = $params['menu'];
        $menuItem = $params['item'];
        $banks = $this->bankService->getAllRegisters(new Bank());
        return $this->renderHTML('/templateListView.html.twig', [
                    'values' => $banks,
                    'route' => $this->route,
                    'titleList' => $this->titleList,                    
                    'labels' => $this->labels,
                    'itemsList' => $this->itemsList,
                    'menuState' => $menuState,
                    'menuItem' => $menuItem
        ]);
    }

    public function getBankDataAction($request) {

        $responseMessage = null;
        $bankSelected = null;
        $menuState = null;
        $menuItem = null;
        if ($request->getMethod() == 'POST') {
            $postData = $request->getParsedBody();
            $bankValidator = v::key('name', v::stringType()->notEmpty())
                    ->key('fiscalId', v::notEmpty())
                    ->key('phone', v::notEmpty())
                    ->key('email', v::notEmpty());
            try {                
                $bankValidator->assert($postData); // true 
                $response = $this->bankService->saveRegister(new Bank(), $postData);
                $menuState = $postData['menu'];
                $menuItem = $postData['menuItem'];                
                $bankSelected = $this->bankService->setInstance(new Bank(), array('id' => $response[0]));
                $responseMessage = $response[1];
            } catch (\Exception $e) {
                $responseMessage = $this->errorService->getError($e);
            }
        } else {
            $params = $request->getQueryParams();
            $menuState = $params['menu'];
            $menuItem = $params['item'];            
            $bankSelected = $this->bankService->setInstance(new Bank(), $params);
        }
        return $this->renderHTML('/templateFormView.html.twig', [
                    'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
                    'responseMessage' => $responseMessage,
                    'value' => $bankSelected,
                    'route' => $this->route,
                    'properties' => $this->properties,
                    'labels' => $this->labels,
                    'titleForm' => $this->titleForm,
                    'menuState' => $menuState,
                    'menuItem' => $menuItem
        ]);
    }

    public function deleteAction(ServerRequest $request) {
        $params = $request->getQueryParams();
        $menuState = $params['menu'];
        $menuItem = $params['menuItem'];
        $this->bankService->deleteRegister(new Bank(), $params['id']);
        return new RedirectResponse('Intranet/banks/list?menu=' . $menuState . '&item=' . $menuItem);
    }

}
