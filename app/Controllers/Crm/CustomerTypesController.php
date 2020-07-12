<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Controllers\Crm;

use App\Controllers\BaseController;
use App\Models\Customer;
use App\Models\CustomerTypes;
use App\Services\Crm\CustomerTypesService;
use Symfony\Component\Config\Definition\Exception\Exception;


class CustomerTypesController extends BaseController
{
    protected $CustomerTypesService;
    
    public function __construct(CustomerTypesService $customerTypesService) {
        parent::__construct();
        $this->CustomerTypesService = $customerTypesService;
    }
    
    public function getIndexAction()
    {
        $customerTypes = CustomerTypes::All();
        return $this->renderHTML('/customers/customerTypesList.html.twig', [
            'currentUser' => $this->currentUser->getCurrentUserEmailAction(),
            'customer_types' => $customerTypes
        ]);
    }
    public function searchCustomerTypesAction($request)
    {
        $searchData = $request->getParsedBody();
        $searchString = $searchData['searchFilter'];        
        $customer = Customer::Where("id", "like", "%".$searchString."%")
                ->orWhere("name", "like", "%".$searchString."%")
                ->orWhere("fiscal_id", "like", "%".$searchString."%")
                ->orWhere("phone", "like", "%".$searchString."%")
                ->orWhere("email", "like", "%".$searchString."%")
                ->get(); 
        
        return $this->renderHTML('/customers/customerTypesList', [
            'currentUser' => $this->currentUser->getCurrentUserEmailAction(),
            'customer_types' => $customer
        ]);

    }
    public function getCustomerTypesDataAction($request)
    {
        $responseMessage = null;
        if($request->getMethod() == 'POST')
        {
            $postData = $request->getParsedBody();
            $postData = $request->getParsedBody();            
            $customerTypeValidator = v::key('name', v::stringType()->notEmpty());           
            try{
                $customerTypeValidator->assert($postData); // true    
                $customerType = CustomerTypes();
                $customerType->description = postData['description'];
                $customerType->save();
                $responseMessage = 'Saved';
            }catch(Exception $e)
            {
                $responseMessage = $e->getMessage();
            }
            
        }
    }
    
    
}
