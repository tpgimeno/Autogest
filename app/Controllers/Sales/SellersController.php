<?php

namespace App\Controllers\Sales;

use App\Controllers\BaseController;
use App\Models\Sellers;
use App\Services\Sales\SellersService;
use Exception;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Diactoros\ServerRequest;
use Respect\Validation\Validator as v;

class SellersController extends BaseController {    
    protected $sellersService;
    
    public function __construct(SellersService $sellersService) {
        parent::__construct();
        $this->sellersService = $sellersService;
        $this->model = new Sellers();
        $this->route = 'sellers';
        $this->titleList = 'Comerciales';
        $this->titleForm = 'Comercial';
        $this->labels = $this->sellersService->getLabelsArray(); 
        $this->itemsList = array('id', 'name', 'email', 'phone');
        $this->properties = $this->sellersService->getModelProperties($this->model);
    }   
    public function getIndexAction($request) {
        return $this->getBaseIndexAction($request, $this->model, null);
    }    
    public function getSellersDataAction($request) {                
        $responseMessage = null;
        if($request->getMethod() == 'POST') {
            $postData = $request->getParsedBody();            
            $sellersValidator = v::key('name', v::stringType()->notEmpty()) 
            ->key('fiscalId', v::notEmpty())
            ->key('phone', v::notEmpty())
            ->key('email', v::stringType()->notEmpty());            
            try{
                $sellersValidator->assert($postData); // true                 
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