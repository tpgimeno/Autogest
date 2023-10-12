<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor. */

namespace App\Controllers\Entitys;

use App\Controllers\BaseController;
use App\Models\Mader;
use App\Services\Entitys\MaderService;
use Exception;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Diactoros\ServerRequest;
use Respect\Validation\Validator as v;

class MadersController extends BaseController {        
    protected $maderService;  
    
    public function __construct(MaderService $maderService) {
        parent::__construct();
        $this->maderService = $maderService;
        $this->model = new Mader();
        $this->route = 'maders';
        $this->titleList = 'Fabricantes';
        $this->titleForm = 'Fabricante';
        $this->labels = $this->maderService->getLabelsArray(); 
        $this->itemsList = array('id', 'name', 'email', 'access', 'phone');
    }    
    public function getIndexAction($request) {
        return $this->getBaseIndexAction($request, $this->model, null);
    }    
    public function getMaderDataAction($request) {                
        $responseMessage = null;
        if($request->getMethod() == 'POST') { 
            $postData = $request->getParsedBody();
            $maderValidator = v::key('name', v::stringType()->notEmpty());            
            try{                
                $maderValidator->assert($postData);               
            }catch(Exception $ex){
                $responseMessage = $ex->getMessage();
            }
            return $this->getBasePostDataAction($request, $this->model, null, $responseMessage);
        }else{
            return $this->getBaseGetDataAction($request, $this->model, null);
        } 
    }   
    public function deleteAction(ServerRequest $request) {
        $params = $request->getQueryParams();
        $this->maderService->deleteRegister($this->model, $params);             
        return new RedirectResponse('/Intranet/buys/maders/list?menu=' . $params['menu'] . '&item=' . $params['item']);
    }    
}
