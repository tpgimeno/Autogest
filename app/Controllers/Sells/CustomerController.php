<?php

namespace App\Controllers\Sells;

use App\Controllers\BaseController;
use App\Models\Customer;
use App\Models\CustomerTypes;
use App\Services\Sells\CustomerService;
use Exception;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Diactoros\ServerRequest;
use Respect\Validation\Validator as v;

class CustomerController extends BaseController
{    
    protected $customerService;
    public function __construct(CustomerService $customerService)
    {
        parent::__construct();
        $this->customerService = $customerService;
    }    
    public function getIndexAction()
    {
        $customer = Customer::All();
        return $this->renderHTML('/sells/customers/customerList.html.twig', [
            'currentUser' => $this->currentUser->getCurrentUserEmailAction(),
            'customers' => $customer
        ]);
    }   
    public function searchCustomerAction($request)
    {
        $searchData = $request->getParsedBody();
        $searchString = $searchData['searchFilter'];        
        $customer = Customer::Where("id", "like", "%".$searchString."%")
                ->orWhere("name", "like", "%".$searchString."%")
                ->orWhere("fiscal_id", "like", "%".$searchString."%")
                ->orWhere("phone", "like", "%".$searchString."%")
                ->orWhere("email", "like", "%".$searchString."%")
                ->get();        
        return $this->renderHTML('/sells/customers/customerList.html.twig', [
            'currentUser' => $this->currentUser->getCurrentUserEmailAction(),
            'customers' => $customer           
        ]);
    }    
    public function getCustomerDataAction($request)
    {                
        $responseMessage = null;
        if($request->getMethod() == 'POST')
        {
            $postData = $request->getParsedBody();
            if($this->validateData($postData)){
                $responseMessage = $this->validateData($postData);
            }                        
            $customer = $this->addCustomerData($postData);
            $responseMessage = $this->saveCustomer($customer);                                   
        }        
        $params = $request->getQueryParams();
        $customerSelected = $this->setCustomer($params);
        $CustomerType = null;
        if($customerSelected)
        {
            $CustomerType = CustomerTypes::find($customerSelected->customerType);            
        }
        $types = CustomerTypes::All();
        return $this->renderHTML('/sells/customers/customerForm.html.twig', [
            'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
            'responseMessage' => $responseMessage,
            'customer' => $customerSelected,
            'customer_type' => $CustomerType,
            'types' => $types
        ]);
    }
    public function validateData($postData)
    {
        $responseMessage = null;
        $customerValidator = v::key('name', v::stringType()->notEmpty()) 
            ->key('fiscal_id', v::notEmpty())
            ->key('phone', v::notEmpty())
            ->key('email', v::stringType()->notEmpty());            
        try {
            $customerValidator->assert($postData); // true 
        } catch(Exception $e) {                
            $responseMessage = $e->getMessage();
        }  
        return $responseMessage;              
    }
    public function addCustomerData($postData)
    {
        $customer = new Customer();                             
        if(Customer::find($postData['id']))
        {
            $customer = Customer::find($postData['id']);           
        }
        $customer->name = $postData['name'];
        $customer->fiscalId = $postData['fiscal_id'];               
        $customer->address = $postData['address'];
        $customer->city = $postData['city'];
        $customer->postalCode = $postData['postal_code'];
        $customer->state = $postData['state'];
        $customer->country = $postData['country'];
        $customer->phone = $postData['phone'];
        $customer->email = $postData['email'];
        $customer->birthDate = $postData['birth'];
        $type = CustomerTypes::where('name', 'like', "%".$postData['type']."%")->first();                
        $customer->customerType = $type->id;
        return $customer;
    }
    public function saveCustomer($customer)
    {
        if(Customer::find($customer->id))
        {
            $customer->update();     
            $responseMessage = 'Updated'; 
        }
        else
        {
            $customer->save();     
            $responseMessage = 'Saved'; 
        } 
        return $responseMessage;
    }
    public function setCustomer($params)
    {
        $customerSelected = null;
        if(isset($params['id']) && $params['id'])
        {            
            $customerSelected = Customer::find($params['id']);            
        }       
        return $customerSelected;
    }
    public function deleteAction(ServerRequest $request)
    {
         
        $this->customerService->deleteCustomer($request->getQueryParams('id'));               
        return new RedirectResponse('/intranet/customers/list');
    }

   

}