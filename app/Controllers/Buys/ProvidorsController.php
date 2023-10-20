<?php

namespace App\Controllers\Buys;

use App\Controllers\BaseController;
use App\Models\Providor;
use App\Services\Buys\ProvidorService;
use Exception;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Diactoros\ServerRequest;
use Respect\Validation\Validator as v;

class ProvidorsController extends BaseController {   
    protected $providorService;
    public function __construct(ProvidorService $providorService) {
        parent::__construct();
        $this->providorService = $providorService;
        $this->model = new Providor();
        $this->route = 'buys/providors';
        $this->titleList = 'Proveedores';
        $this->titleForm = 'Proveedor';
        $this->labels = $this->providorService->getLabelsArray(); 
        $this->itemsList = array('id', 'name', 'email', 'phone');
        $this->properties = $this->providorService->getModelProperties($this->model);
    }    
    public function getIndexAction($request) {
        return $this->getBaseIndexAction($request, $this->model, null);
    } 
   
    public function getProvidorDataAction($request) {                
        $responseMessage = null;
        if($request->getMethod() == 'POST') {
            $postData = $request->getParsedBody();            
            $providorValidator = v::key('name', v::stringType()->notEmpty()) 
            ->key('fiscalId', v::notEmpty())
            ->key('phone', v::notEmpty())
            ->key('email', v::stringType()->notEmpty());            
            try{
                $providorValidator->assert($postData); // true                 
            }catch(Exception $e){                
                $responseMessage = $e->getMessage();
            }     
            return $this->getBasePostDataAction($request, $this->model, null, $responseMessage);
        }else{
            return $this->getBaseGetDataAction($request, $this->model, null);
        }
    }
    public function deleteAction(ServerRequest $request) {         
        return $this->deleteItemAction($request, $this->model);
    }    
}