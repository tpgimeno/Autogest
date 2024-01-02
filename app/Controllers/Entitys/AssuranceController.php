<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Controllers\Entitys;

use App\Controllers\BaseController;
use App\Models\Assurances;
use App\Models\Customer;
use App\Models\Vehicle;
use App\Services\Entitys\AssuranceService;
use Exception;
use Laminas\Diactoros\ServerRequest;
use Respect\Validation\Validator as v;

/**
 * Description of AssuranceController
 *
 * @author tonyl
 */
class AssuranceController extends BaseController {
    protected $assuranceService;
    
    public function __construct(AssuranceService $assuranceService) {
        parent::__construct();
        $this->assuranceService = $assuranceService;
        $this->model = new Assurances();
        $this->route = 'assurances';
        $this->titleList = 'Pólizas de Seguro';
        $this->titleForm = 'Póliza de Seguro';
        $this->labels = $this->assuranceService->getLabelsArray(); 
        $this->itemsList = array('id','ref', 'effectDate', 'owner_id', 'object_id', 'price');
    }    
    public function getIndexAction($request) {
        $values = $this->assuranceService->list();
        return $this->getBaseIndexAction($request, $this->model, $values);
    }  
    public function getAssuranceDataAction($request) {                
        $responseMessage = null;        
        $iterables = ['customers' => $this->assuranceService->getAllRegisters(new Customer()),
            'vehicles' => $this->assuranceService->getAllRegisters(new Vehicle())
            ];
        if($request->getMethod() == 'POST') {
            $postData = $request->getParsedBody();            
            $assuranceValidator = v::key('object_id', v::notEmpty()) 
            ->key('owner_id', v::notEmpty())
            ->key('ref', v::notEmpty());                      
            try{
                $assuranceValidator->assert($postData); // true                                    
            }catch(Exception $e) {                
                $responseMessage = $e->getMessage();
            } 
            return $this->getBasePostDataAction($request, $this->model, $iterables, $responseMessage);
        }else{
            return $this->getBaseGetDataAction($request, $this->model, $iterables);
        }
    }
    public function deleteAction(ServerRequest $request) { 
        return $this->deleteItemAction($request, new Assurances());
    }
}
