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
    protected $list = '/Intranet/maders/list';
    protected $tab = 'home';
    protected $title = 'Fabricantes';
    protected $save = "/Intranet/maders/save";
    protected $formName = "madersForm";
    protected $inputs = ['id' => ['id' => 'inputID', 'name' => 'id', 'title' => 'ID'],        
        'fiscalId' => ['id' => 'inputFiscalId', 'name' => 'fiscalId', 'title' => 'NIF/CIF'],
        'name' => ['id' => 'inputName', 'name' => 'name', 'title' => 'Nombre'],
        'address' => ['id' => 'inputAdress', 'name' => 'address', 'title' => 'Dirección'],
        'city' => ['id' => 'inputCity', 'name' => 'city', 'title' => 'Población'],
        'postalCode' => ['id' => 'inputZip', 'name' => 'postalCode', 'title' => 'Código Postal'],        
        'state' => ['id' => 'inputState', 'name' => 'state', 'title' => 'Provincia'],
        'country' => ['id' => 'inputCountry', 'name' => 'country', 'title' => 'Pais'],
        'phone' => ['id' => 'inputPhone', 'name' => 'phone', 'title' => 'Telefono'],
        'email' => ['id' => 'inputEmail', 'name' => 'email', 'title' => 'Email'],
        'site' => ['id' => 'inputWeb', 'name' => 'site', 'title' => 'Página Web']];
    public function __construct(MaderService $maderService) {
        parent::__construct();
        $this->maderService = $maderService;
    }    
    public function getIndexAction() {
        $maders = $this->maderService->getAllRegisters(new Mader());
        return $this->renderHTML('/Entitys/maders/madersList.html.twig', [
            'list' => $this->list,
            'tab' => $this->tab,
            'title' => $this->title,
            'maders' => $maders
        ]);
    }    
    public function getMaderDataAction($request) {                
        $responseMessage = null;
        if($request->getMethod() == 'POST') {       
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
            'inputs' => $this->inputs,
            'save' => $this->save,
            'list' => $this->list,
            'formName' => $this->formName,
            'tab' => $this->tab,
            'title' => $this->title,
            'value' => $maderSelected
        ]);
    }   
    public function deleteAction(ServerRequest $request) {         
        $this->maderService->deleteRegister(new Mader(), $request->getQueryParams('id'));             
        return new RedirectResponse('/Intranet/buys/maders/list');
    }    
}
