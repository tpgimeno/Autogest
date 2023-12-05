<?php

namespace App\Controllers\Sales;

use App\Controllers\BaseController;
use App\Models\Customer;
use App\Models\CustomerTypes;
use App\Services\Sales\CustomerService;
use Exception;
use Laminas\Diactoros\ServerRequest;
use Respect\Validation\Validator as v;

class CustomerController extends BaseController {    
    protected $customerService;
    
    public function __construct(CustomerService $customerService) {
        parent::__construct();
        $this->customerService = $customerService;
        $this->model = new Customer();
        $this->route = 'customers';
        $this->titleList = 'Clientes';
        $this->titleForm = 'Cliente';
        $this->labels = $this->customerService->getLabelsArray(); 
        $this->itemsList = array('id', 'name', 'email', 'phone');
        $this->properties = $this->customerService->getModelProperties($this->model);
    }    
    public function getIndexAction($request) {
        return $this->getBaseIndexAction($request, $this->model, null);
    }   
    
    public function getCustomerDataAction($request) {                
        $responseMessage = null;
        $iterables = ['customerType' => $this->customerService->getAllRegisters(new CustomerTypes())];
        if($request->getMethod() == 'POST') {
            $postData = $request->getParsedBody();
            $customerValidator = v::key('name', v::stringType()->notEmpty()) 
            ->key('fiscalId', v::notEmpty())
            ->key('phone', v::notEmpty())
            ->key('email', v::stringType()->notEmpty());      
             try {
                $customerValidator->assert($postData); // true                
            } catch(Exception $e) {                
                $responseMessage = $e->getMessage();
            }  
            return $this->getBasePostDataAction($request, $this->model, $iterables, $responseMessage);
        }else{
            return $this->getBaseGetDataAction($request, $this->model, $iterables);    
        }       
        
    }   
    public function deleteAction(ServerRequest $request) {         
        return $this->deleteItemAction($request, $this->model);
    }

   

}