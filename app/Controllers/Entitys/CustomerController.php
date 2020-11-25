<?php

namespace App\Controllers\Entitys;

use App\Controllers\BaseController;
use App\Models\Customer;
use App\Models\CustomerTypes;
use App\Services\CustomerService;
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
        return $this->renderHTML('/customers/customerList.html.twig', [
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
        
        return $this->renderHTML('/customers/customerList.html.twig', [
            'currentUser' => $this->currentUser->getCurrentUserEmailAction(),
            'customers' => $customer,
           
        ]);
    }
    
    public function getCustomerDataAction($request)
    {                
        $responseMessage = null;
        if($request->getMethod() == 'POST')
        {
            $postData = $request->getParsedBody();
            
            $customerValidator = v::key('name', v::stringType()->notEmpty()) 
            ->key('fiscal_id', v::notEmpty())
            ->key('phone', v::notEmpty())
            ->key('email', v::stringType()->notEmpty());            
            try{
                $customerValidator->assert($postData); // true 
                $customer = new Customer();
                $customer->id = $postData['id'];
                $selected = false;
                
                if(Customer::find($postData['id']))
                {
                    
                    $customer = Customer::find($postData['id']);
                    $selected = true;
                }
               
                $customer->name = $postData['name'];
                $customer->fiscal_id = $postData['fiscal_id'];               
                $customer->address = $postData['address'];
                $customer->city = $postData['city'];
                $customer->postal_code = $postData['postal_code'];
                $customer->state = $postData['state'];
                $customer->country = $postData['country'];
                $customer->phone = $postData['phone'];
                $customer->email = $postData['email'];
                $customer->birth_date = $postData['birth'];
                $type = CustomerTypes::where('name', 'like', "%".$postData['type']."%")->first();                
                $customer->customer_type = $type->id;
                if($selected == true)
                {
                    $customer->update();     
                    $responseMessage = 'Updated'; 
                }
                else
                {
                    $customer->save();     
                    $responseMessage = 'Saved'; 
                }                    
            }catch(Exception $e){                
                $responseMessage = $e->getMessage();
            }              
        }
        $customerSelected = null;
        $params = $request->getQueryParams();
        if($params)
        {            
            $customerSelected = Customer::find($params['id']);            
        }
        $types = CustomerTypes::All();
        $CustomerType = null;
        if($customerSelected)
        {
            $CustomerType = CustomerTypes::find($customerSelected->customer_type);            
        }
        return $this->renderHTML('/customers/customerForm.html.twig', [
            'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
            'responseMessage' => $responseMessage,
            'customer' => $customerSelected,
            'customer_type' => $CustomerType,
            'types' => $types
        ]);
    }

    public function deleteAction(ServerRequest $request)
    {
         
        $this->customerService->deleteCustomer($request->getQueryParams('id'));               
        return new RedirectResponse('/intranet/customers/list');
    }

   

}