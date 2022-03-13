<?php

namespace App\Controllers\Vehicle;

use App\Controllers\BaseController;
use App\Models\Works;
use App\Services\Vehicle\WorksService;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Diactoros\ServerRequest;
use Respect\Validation\Validator as v;
use Exception;

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
    protected $list = "/Intranet/vehicles/works/list";
    protected $tab = "buys";
    protected $title = "Trabajos";
    protected $save = "/Intranet/vehicles/works/save";
    protected $formName = 'worksForm';  
    protected $search = '/Intranet/vehicles/works/search';
    protected $script = 'js/works.js';
    protected $inputs = ['id' => ['id' => 'inputId', 'name' => 'id', 'title' => 'ID'], 
        'reference' => ['id' => 'inputRef', 'name' => 'reference', 'title' => 'Referencia'],        
        'description' => ['id' => 'inputDescription', 'name' => 'description', 'title' => 'Descripcion'],
        'observations' => ['id' => 'observations', 'name' => 'observations', 'title' => 'Observaciones'],
        'pvc' => ['id' => 'inputPvc', 'name' => 'pvc', 'title' => 'Precio Compra'],
        'pvp' => ['id' => 'inputPvp', 'name' => 'pvp', 'title' => 'Precio Venta'],
        'tvaBuy' => ['id' => 'inputTvaBuy', 'name' => 'tvaBuy', 'title' => 'Iva Compra'],
        'tvaSell' => ['id' => 'inputTvaSell', 'name' => 'tvaSell', 'title' => 'Iva Venta'],
        'totalBuy' => ['id' => 'inputTotalBuy', 'name' => 'totalBuy', 'title' => 'Total Compra'],
        'totalSell' => ['id' => 'inputTotalSell', 'name' => 'totalSell', 'title' => 'Total Venta']];
    public function __construct(WorksService $worksService) {
        parent::__construct();
        $this->worksService = $worksService;
    }
    public function getIndexAction() {
        $works = $this->worksService->getAllRegisters(new Works());       
        return $this->renderHTML('/vehicles/works/worksList.html.twig', [
            'title' => $this->title,
            'list' => $this->list,
            'tab' => $this->tab,
            'search' => $this->search,
            'works' => $works
        ]);
    }  
    public function searchWorkAction($request) {
        $searchData = $request->getParsedBody();      
        $works = $this->worksService->searchWorks($searchData['searchFilter']);
        return $this->renderHTML('/vehicles/works/worksList.html.twig', [
            'title' => $this->title,
            'list' => $this->list,
            'tab' => $this->tab,
            'search' => $this->search,
            'works' => $works
        ]);
    }    
    public function getWorkDataAction($request) {
        $responseMessage = null;    
        if($request->getMethod() == 'POST') {
           $worksValidator = v::key('description', v::stringType()->notEmpty());
           $postData = $request->getParsedBody(); 
           try{
               $worksValidator->assert($postData);
               $responseMessage = $this->worksService->saveRegister(new Works(), $postData); 
           } catch (Exception $ex) {
               $responseMessage = $ex->getMessage();
           }                     
        }        
        $selectedWork = $this->worksService->setInstance(new Works(), $request->getQueryParams('id'));                  
        return $this->renderHTML('/vehicles/works/worksForm.html.twig', [
            'value' => $selectedWork,
            'title' => $this->title,
            'list' => $this->list,
            'tab' => $this->tab,
            'save' => $this->save,
            'formName' => $this->formName,
            'inputs' => $this->inputs,
            'script' => $this->script,
            'responseMessage' => $responseMessage
        ]);
    }  
    public function searchWorks(){
        
    }
    public function deleteAction(ServerRequest $request) {         
        $this->worksService->deleteRegister(new Works(), $request->getQueryParams('id'));               
        return new RedirectResponse('/Intranet/vehicles/works/list');
    }  
}
