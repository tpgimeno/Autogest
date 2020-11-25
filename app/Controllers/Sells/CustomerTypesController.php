<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Controllers\Crm;

use App\Controllers\BaseController;
use App\Models\CustomerTypes;
use App\Services\Crm\CustomerTypesService;
use Respect\Validation\Validator as v;
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
        $customer_types = CustomerTypes::Where("name", "like", "%".$searchString."%")                
                ->get(); 
        
        return $this->renderHTML('/customers/customerTypesList.html.twig', [
            'currentUser' => $this->currentUser->getCurrentUserEmailAction(),
            'customer_types' => $customer_types
        ]);

    }
    public function getCustomerTypesDataAction($request)
    {
        $responseMessage = null;
        if($request->getMethod() == 'POST')
        {
            $postData = $request->getParsedBody();                       
            $customerTypeValidator = v::key('name', v::stringType()->notEmpty());           
            try{
                $customerTypeValidator->assert($postData); // true    
                $customerType = new CustomerTypes();
                $customerType->name = $postData['name'];
                $customerType->save();
                $responseMessage = 'Saved';
            }catch(Exception $e)
            {
                $responseMessage = $e->getMessage();
            }            
        }
        return $this->renderHTML('/customers/customerTypesForm.html.twig', [
            'currentUser' => $this->currentUser->getCurrentUserEmailAction(),
            'responseMessage' => $responseMessage
        ]);
    }    
}
