<?php

namespace App\Controllers\Entitys;

use App\Controllers\BaseController;
use Respect\Validation\Validator as v;
use App\Models\Store;
use App\Services\Entitys\StoreService;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Response\RedirectResponse;

class StoreController extends BaseController {    
    protected $storeService;
    public function __construct(StoreService $storeService) {
        parent::__construct();
        $this->storeService = $storeService;
    }        
    public function getIndexAction() {
        $store = $this->storeService->getAllRegisters(new Store());
        return $this->renderHTML('/Entitys/stores/storeList.html.twig', [
            'stores' => $store
        ]);
    }  
    public function searchCompaniesAction($request){
        $searchData = $request->getParsedBody();
        $store = $this->storeService->searchStore($searchData['searchFilter']);
        return $this->renderHTML('/Entitys/stores/storeList.html.twig', [
            'stores' => $store
        ]);
    }    
    public function getStoreDataAction($request) {                
        $responseMessage = null;
        if($request->getMethod() == 'POST') {
            $postData = $request->getParsedBody();            
            $storeValidator = v::key('name', v::stringType()->notEmpty());           
            try {
                $storeValidator->assert($postData); // true 
                $responseMessage = $this->storeService->saveRegister(new Store(), $postData);
            }catch(\Exception $e){                
                $responseMessage = $e->getMessage();
            }              
        }
        $storeSelected = null;
        if($request->getQueryParams('id')) {
            $storeSelected = $this->storeService->setInstance(new Store(), $request->getQueryParams());
        }
        return $this->renderHTML('/Entitys/stores/storeForm.html.twig', [
            'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
            'responseMessage' => $responseMessage,
            'store' => $storeSelected
        ]);
    }   
    public function deleteAction(ServerRequest $request) {         
        $this->storeService->deleteRegister(new Store(), $request->getQueryParams('id'));             
        return new RedirectResponse('/Intranet/store/list');
    }
}