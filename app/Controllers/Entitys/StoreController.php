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
        $this->model = new Store();
        $this->route = 'stores';
        $this->titleList = 'Almacenes';
        $this->titleForm = 'Almacén';
        $this->labels = $this->storeService->getLabelsArray(); 
        $this->itemsList = array('id', 'name', 'address', 'city', 'phone');
    }        
    public function getIndexAction($request) {
        return $this->getBaseIndexAction($request, $this->model);
    }  
    
    public function getStoreDataAction($request) {                
        $responseMessage = null;
        if($request->getMethod() == 'POST') {
            $postData = $request->getParsedBody();            
            $storeValidator = v::key('name', v::stringType()->notEmpty());           
            try {
                $storeValidator->assert($postData); // true                 
            }catch(\Exception $e){                
                $responseMessage = $e->getMessage();
            } 
            return $this->getBasePostDataAction($request, $this->model, null, $responseMessage);
        }else{
            return $this->getBaseGetDataAction($request, $this->model, null);
        }
    }   
    public function deleteAction(ServerRequest $request) { 
        $params = $request->getQueryParams();
        $this->storeService->deleteRegister(new Store(), $params);             
        return new RedirectResponse('/Intranet/stores/list?menu=' . $params['menu'] . '&item=' . $params['item']);
    }
}