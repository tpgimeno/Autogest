<?php

namespace App\Controllers\Entitys;

use App\Controllers\BaseController;
use App\Models\Customer;
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
        return $this->renderHTML('/customers/customerList.twig', [
            'currentUser' => $this->currenUser->getCurrentUserEmailAction(),
            'customers' => $customer
        ]);
    }   
    public function searchCustomerAction($request)
    {
        $searchData = $request->getParsedBody();
        $searchString = $searchData['searchFilter'];        
        $customer = Customer::Where("name", "like", "%".$searchString."%")
                ->orWhere("fiscal_id", "like", "%".$searchString."%")
                ->orWhere("phone", "like", "%".$searchString."%")
                ->orWhere("email", "like", "%".$searchString."%")
                ->get();       

        return $this->renderHTML('/customers/customerList.twig', [
            'currentUser' => $this->currenUser->getCurrentUserEmailAction(),
            'customers' => $customer
                
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
                $customer->name = $postData['name'];
                $customer->fiscal_id = $postData['fiscal_id'];               
                $customer->address = $postData['address'];
                $customer->city = $postData['city'];
                $customer->postal_code = $postData['postal_code'];
                $customer->state = $postData['state'];
                $customer->country = $postData['country'];
                $customer->phone = $postData['phone'];
                $customer->email = $postData['email'];               
                $customer->save();     
                $responseMessage = 'Saved';     
            }catch(Exception $e){                
                $responseMessage = $e->getMessage();
            }              
        }
        $customerSelected = null;
        if($_GET)
        {
            $customerSelected = Customer::find($_GET['id']);
        }
        return $this->renderHTML('/customers/customerForm.twig', [
            'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
            'responseMessage' => $responseMessage,
            'customer' => $customerSelected
        ]);
    }

    public function deleteAction(ServerRequest $request)
    {
         
        $this->customerService->deleteCustomer($request->getQueryParams('id'));               
        return new RedirectResponse('/intranet/customers/list');
    }

   

}