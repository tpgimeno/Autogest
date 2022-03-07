<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Controllers\Vehicle;

use App\Controllers\BaseController;
use App\Models\Accesories;
use App\Services\ErrorService;
use App\Services\Vehicle\AccesoriesService;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Diactoros\ServerRequest;
use Respect\Validation\Validator as v;
use ZipStream\Exception;



/**
 * Description of AccesoriesController
 *
 * @author tonyl
 */
class AccesoriesController extends BaseController
{
    protected $accesoriesService;
    protected $list = '/Intranet/vehicles/accesories/list';
    protected $tab = 'buys';
    protected $title = 'Accesorios';
    protected $save = "/Intranet/vehicles/accesories/save";
    protected $formName = "accesoriesForm";
    protected $inputs = ['id' => ['id' => 'inputID', 'name' => 'id', 'title' => 'ID'],  
        'name' => ['id' => 'inputName', 'name' => 'name', 'title' => 'Nombre']];    
    public function __construct(AccesoriesService $accesoriesService, ErrorService $errorService) {
        parent::__construct();
        $this->accesoriesService = $accesoriesService;
        $this->errorService = $errorService;
    }
    public function getIndexAction() {
        $accesories = $this->accesoriesService->getAccesories();       
        return $this->renderHTML('/vehicles/accesories/accesoriesList.html.twig', [
            'list' => $this->list,
            'tab' => $this->tab,
            'title' => $this->title,
            'accesories' => $accesories
        ]);
    }    
    public function getAccesoryDataAction($request) {   
        $responseMessage = null;        
        if($request->getMethod() == 'POST'){
            $postData = $request->getParsedBody();
            $accesoriesValidator = v::key('name', v::stringType()->notEmpty());
            try{
                $accesoriesValidator->assert($postData);
                $keyString = $this->accesoriesService->normalizeKeyString($postData['name']);
                array_push($postData, $keyString);
                $responseMessage = $this->accesoriesService->saveRegister(new Accesories(), $postData);
            } catch (Exception $ex) {
                $responseMessage = $ex->getMessage();
            } 
        }                            
        $selected_accesory = $this->accesoriesService->setInstance(new Accesories(), $request->getQueryParams('id'));
        return $this->renderHTML('/vehicles/accesories/accesoriesForm.html.twig', [
            'value' => $selected_accesory,
            'inputs' => $this->inputs,
            'save' => $this->save,
            'list' => $this->list,
            'formName' => $this->formName,
            'tab' => $this->tab,
            'title' => $this->title,
            'responseMessage' => $responseMessage
        ]);
    }    
    public function deleteAction(ServerRequest $request) {         
        $this->accesoriesService->deleteRegister(new Accesories(), $request->getQueryParams('id'));               
        return new RedirectResponse($this->list);
    }  
}
