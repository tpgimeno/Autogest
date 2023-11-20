<?php

namespace App\Controllers\Vehicle;

use App\Controllers\BaseController;
use App\Models\Works;
use App\Services\Vehicle\WorksService;
use Exception;
use Laminas\Diactoros\ServerRequest;
use Respect\Validation\Validator as v;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of WorksController
 *
 * @author tonyl
 */
class WorksController extends BaseController {
    protected $worksService; 
    
    public function __construct(WorksService $worksService) {
        parent::__construct();
        $this->worksService = $worksService;
        $this->model = new Works();
        $this->route = 'vehicles/works';
        $this->titleList = 'Trabajos';
        $this->titleForm = 'Trabajo';
        $this->labels = $this->worksService->getLabelsArray(); 
        $this->itemsList = array('id', 'reference', 'name', 'pvp');
        $this->properties = $this->worksService->getModelProperties($this->model);
    }
    public function getIndexAction($request) {
        return $this->getBaseIndexAction($request, $this->model, null);
    } 
    
    public function getWorkDataAction($request) {
        $responseMessage = null;    
        if($request->getMethod() == 'POST') {
           $worksValidator = v::key('description', v::stringType()->notEmpty());
           $postData = $request->getParsedBody(); 
           
           try{
               $worksValidator->assert($postData);               
           } catch (Exception $ex) {
               $responseMessage = $ex->getMessage();
           } 
           return $this->getBasePostDataAction($request, $this->model, null, $responseMessage);
        }else{
           return $this->getBaseGetDataAction($request, $this->model, null); 
        }
    }  
    
    public function deleteAction(ServerRequest $request) {         
        $this->deleteItemAction($request, $this->model);
    }  
}
