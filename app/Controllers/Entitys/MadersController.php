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
use Illuminate\Database\QueryException;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Diactoros\ServerRequest;
use Respect\Validation\Validator as v;

class MadersController extends BaseController {        
    protected $maderService;    
    public function __construct(MaderService $maderService) {
        parent::__construct();
        $this->maderService = $maderService;
    }    
    public function getIndexAction() {
        $maders = $this->maderService->getAllRegisters(new Mader());
        return $this->renderHTML('/Entitys/maders/madersList.html.twig', [
            'maders' => $maders
        ]);
    }    
    public function getMaderDataAction($request) {                
        $responseMessage = null;
        if($request->getMethod() == 'POST')
        {       
            $maderValidator = v::key('name', v::stringType()->notEmpty());            
            try{
                $postData = $request->getParsedBody();            
                $maderValidator->assert($postData);          
                $responseMessage = $this->maderService->saveRegister(new Mader(), $postData);
            }catch(Exception $ex){
                $responseMessage = $ex->getMessage();
            }
        }       
        $maderSelected = $this->maderService->setInstance(new Mader(), $request->getQueryParams('id'));
        return $this->renderHTML('/Entitys/maders/madersForm.html.twig', [
            'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
            'responseMessage' => $responseMessage,
            'mader' => $maderSelected
        ]);
    }   
    public function deleteAction(ServerRequest $request) {         
        $this->maderService->deleteRegister(new Mader(), $request->getQueryParams('id'));             
        return new RedirectResponse('/Intranet/buys/maders/list');
    }    
}
