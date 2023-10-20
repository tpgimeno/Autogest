<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Controllers\Sales;

use App\Controllers\BaseController;
use App\Models\CustomerTypes;
use App\Services\Sales\CustomerTypesService;
use Laminas\Diactoros\Response\RedirectResponse;
use Respect\Validation\Validator as v;
use Symfony\Component\Config\Definition\Exception\Exception;

class CustomerTypesController extends BaseController {
    protected $customerTypesService;
    
    public function __construct(CustomerTypesService $customerTypesService) {
        parent::__construct();
        $this->customerTypesService = $customerTypesService;
        $this->model = new CustomerTypes();
        $this->route = 'customers/type';
        $this->titleList = 'Tipos de Clientes';
        $this->titleForm = 'Tipo de Cliente';
        $this->labels = $this->customerTypesService->getLabelsArray(); 
        $this->itemsList = array('id', 'name');
        $this->properties = $this->customerTypesService->getModelProperties($this->model);
    }    
    public function getIndexAction($request) {
        return $this->getBaseIndexAction($request, $this->model, null);
    }    
    public function getCustomerTypesDataAction($request) {
        $responseMessage = null;
        if($request->getMethod() == 'POST') {
            $postData = $request->getParsedBody();                       
            $customerTypeValidator = v::key('name', v::stringType()->notEmpty());           
            try{
                $customerTypeValidator->assert($postData); // true 
            }catch(Exception $e) {
                $responseMessage = $e->getMessage();
            } 
            return $this->getBasePostDataAction($request, $this->model, null, $responseMessage);
        }else{
            return $this->getBaseGetDataAction($request, $this->model, null);
        }
    }
    public function deleteAction($request){
        return $this->deleteItemAction($request, $this->model);
    }
}
