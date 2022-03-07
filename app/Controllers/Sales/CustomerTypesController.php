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
    protected $CustomerTypesService;
    protected $list = '/Intranet/customers/type/list';
    protected $tab = 'sales';
    protected $title = 'Tipos de Cliente';
    protected $save = "/Intranet/customers/type/save";
    protected $formName = "customerTypesForm";
    protected $inputs = ['id' => ['id' => 'inputID', 'name' => 'id', 'title' => 'ID'],  
        'name' => ['id' => 'inputName', 'name' => 'name', 'title' => 'Nombre']];
    public function __construct(CustomerTypesService $customerTypesService) {
        parent::__construct();
        $this->CustomerTypesService = $customerTypesService;
    }    
    public function getIndexAction() {
        $customerTypes = $this->CustomerTypesService->getAllRegisters(new CustomerTypes());
        return $this->renderHTML('/sales/customers/customerTypesList.html.twig', [
            'currentUser' => $this->currentUser->getCurrentUserEmailAction(),
            'list' => $this->list,
            'tab' => $this->tab,
            'title' => $this->title,
            'customer_types' => $customerTypes
        ]);
    }    
    public function getCustomerTypesDataAction($request) {
        $responseMessage = null;
        if($request->getMethod() == 'POST') {
            $postData = $request->getParsedBody();                       
            $customerTypeValidator = v::key('name', v::stringType()->notEmpty());           
            try{
                $customerTypeValidator->assert($postData); // true    
                $responseMessage = $this->CustomerTypesService->saveRegister(new CustomerTypes(), $postData);
            }catch(Exception $e) {
                $responseMessage = $e->getMessage();
            }            
        }
        $selectedCustomerType = $this->CustomerTypesService->setInstance(new CustomerTypes(), $request->getQueryParams('id'));
        return $this->renderHTML('/sales/customers/customerTypesForm.html.twig', [
            'currentUser' => $this->currentUser->getCurrentUserEmailAction(),
            'inputs' => $this->inputs,
            'save' => $this->save,
            'list' => $this->list,
            'formName' => $this->formName,
            'tab' => $this->tab,
            'title' => $this->title,
            'value' => $selectedCustomerType,
            'responseMessage' => $responseMessage
        ]);
    }
    public function deleteAction($request){
        $this->CustomerTypesService->deleteRegister(new CustomerTypes(), $request->getQueryParams('id'));
        return new RedirectResponse($this->list);
    }
}
