<?php

namespace App\Controllers\Sales;

use App\Controllers\BaseController;
use App\Models\Customer;
use App\Services\Sales\CustomerService;
use Exception;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Diactoros\ServerRequest;
use Respect\Validation\Validator as v;

class CustomerController extends BaseController {    
    protected $customerService;
    protected $list = '/Intranet/customers/list';
    protected $tab = 'sales';
    protected $title = 'Clientes';
    protected $save = "/Intranet/customers/save";
    protected $search = "/Intranet/customers/search";
    protected $formName = "customersForm";
    protected $inputs = ['id' => ['id' => 'inputID', 'name' => 'id', 'title' => 'ID'],  
        'name' => ['id' => 'inputName', 'name' => 'name', 'title' => 'Nombre'],
        'fiscalId' => ['id' => 'inputFiscalId', 'name' => 'fiscalId', 'title' => 'NIF/CIF'],        
        'address' => ['id' => 'inputAdress', 'name' => 'address', 'title' => 'Dirección'],
        'city' => ['id' => 'inputCity', 'name' => 'city', 'title' => 'Población'],
        'postalCode' => ['id' => 'inputZip', 'name' => 'postalCode', 'title' => 'Código Postal'],        
        'state' => ['id' => 'inputState', 'name' => 'state', 'title' => 'Provincia'],
        'country' => ['id' => 'inputCountry', 'name' => 'country', 'title' => 'Pais'],
        'phone' => ['id' => 'inputPhone', 'name' => 'phone', 'title' => 'Telefono'],
        'email' => ['id' => 'inputEmail', 'name' => 'email', 'title' => 'Email'],
        'birthDate' => ['id' => 'inputBirthDate', 'name' => 'birthDate', 'title' => 'Fecha Nacimiento'],
        'selectCustomerType' => ['id' => 'selectCustomerType', 'name' => 'customerType', 'title' => 'Tipo de Cliente']];
    public function __construct(CustomerService $customerService) {
        parent::__construct();
        $this->customerService = $customerService;
    }    
    public function getIndexAction() {
        $customer = $this->customerService->getCustomers();
        return $this->renderHTML('/sales/customers/customerList.html.twig', [
            'currentUser' => $this->currentUser->getCurrentUserEmailAction(),
            'list' => $this->list,
            'tab' => $this->tab,
            'search' => $this->search,
            'title' => $this->title,
            'customers' => $customer
        ]);
    }   
    public function searchCustomerAction($request) {
        $searchData = $request->getParsedBody();
        $searchString = $searchData['searchFilter'];        
        $customer = $this->customerService->searchCustomer($searchString);     
        return $this->renderHTML('/sales/customers/customerList.html.twig', [
            'currentUser' => $this->currentUser->getCurrentUserEmailAction(),
            'list' => $this->list,
            'tab' => $this->tab,
            'search' => $this->search,
            'title' => $this->title,
            'customers' => $customer           
        ]);
    }    
    public function getCustomerDataAction($request) {                
        $responseMessage = null;
        if($request->getMethod() == 'POST') {
            $postData = $request->getParsedBody();
            $customerValidator = v::key('name', v::stringType()->notEmpty()) 
            ->key('fiscalId', v::notEmpty())
            ->key('phone', v::notEmpty())
            ->key('email', v::stringType()->notEmpty());      
             try {
                $customerValidator->assert($postData); // true
                $postData['customerType'] = $this->customerService->getCustomerTypesByName($postData['customerType']);
                $responseMessage = $this->customerService->saveRegister(new Customer(), $postData);
            } catch(Exception $e) {                
                $responseMessage = $e->getMessage();
            }                                         
        }        
        $customerSelected = $this->customerService->setInstance(new Customer(), $request->getQueryParams('id'));
        $types = $this->customerService->getCustomerTypes();
        return $this->renderHTML('/sales/customers/customerForm.html.twig', [
            'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
            'responseMessage' => $responseMessage,
            'value' => $customerSelected,
            'inputs' => $this->inputs,
            'save' => $this->save,
            'list' => $this->list,
            'formName' => $this->formName,
            'tab' => $this->tab,
            'title' => $this->title,           
            'types' => $types
        ]);
    }   
    public function deleteAction(ServerRequest $request) {         
        $this->customerService->deleteRegister(new Customer(), $request->getQueryParams('id'));            
        return new RedirectResponse('/Intranet/customers/list');
    }

   

}