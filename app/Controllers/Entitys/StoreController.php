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
    protected $list = '/Intranet/stores/list';
    protected $tab = 'home';
    protected $title = 'Almacenes';
    protected $save = "/Intranet/stores/save";
    protected $formName = "storesForm";
    protected $search = "/Intranet/stores/search";
    protected $inputs = ['id' => ['id' => 'inputID', 'name' => 'id', 'title' => 'ID'],
        'name' => ['id' => 'inputName', 'name' => 'name', 'title' => 'Nombre'],
        'address' => ['id' => 'inputAdress', 'name' => 'address', 'title' => 'Dirección'],
        'postalCode' => ['id' => 'inputZip', 'name' => 'postalCode', 'title' => 'Código Postal'],
        'city' => ['id' => 'inputCity', 'name' => 'city', 'title' => 'Población'],
        'state' => ['id' => 'inputState', 'name' => 'state', 'title' => 'Provincia'],
        'country' => ['id' => 'inputCountry', 'name' => 'country', 'title' => 'Pais'],
        'phone' => ['id' => 'inputPhone', 'name' => 'phone', 'title' => 'Telefono'],
        'email' => ['id' => 'inputEmail', 'name' => 'email', 'title' => 'Email']];
    public function __construct(StoreService $storeService) {
        parent::__construct();
        $this->storeService = $storeService;
    }        
    public function getIndexAction() {
        $store = $this->storeService->getAllRegisters(new Store());
        return $this->renderHTML('/Entitys/stores/storeList.html.twig', [
            'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
            'title' => $this->title,
            'tab' => $this->tab,
            'list' => $this->list,
            'stores' => $store
        ]);
    }  
    public function searchCompaniesAction($request){
        $searchData = $request->getParsedBody();
        $store = $this->storeService->searchStore($searchData['searchFilter']);
        return $this->renderHTML('/Entitys/stores/storeList.html.twig', [
            'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
            'title' => $this->title,
            'tab' => $this->tab,
            'list' => $this->list,
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
            'inputs' => $this->inputs,
            'save' => $this->save,
            'list' => $this->list,
            'formName' => $this->formName,
            'tab' => $this->tab,
            'title' => $this->title,
            'value' => $storeSelected
        ]);
    }   
    public function deleteAction(ServerRequest $request) {         
        $this->storeService->deleteRegister(new Store(), $request->getQueryParams('id'));             
        return new RedirectResponse('/Intranet/stores/list');
    }
}