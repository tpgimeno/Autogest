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
    
    public function __construct(AccesoriesService $accesoriesService, ErrorService $errorService) {
        parent::__construct();
        $this->accesoriesService = $accesoriesService;
        $this->errorService = $errorService;
        $this->model = new Accesories();
        $this->route = 'vehicles/accesories';
        $this->titleList = 'Accesorios';
        $this->titleForm = 'Accesorio';
        $this->labels = $this->accesoriesService->getLabelsArray(); 
        $this->itemsList = array('id', 'name');
        $this->properties = $this->accesoriesService->getModelProperties($this->model);
    }
    public function getIndexAction($request) {
        return $this->getBaseIndexAction($request, $this->model, null);
    }    
    public function getAccesoryDataAction($request) {   
        $responseMessage = null;        
        if($request->getMethod() == 'POST'){
            $postData = $request->getParsedBody();
            $accesoriesValidator = v::key('name', v::stringType()->notEmpty());
            try{
                $accesoriesValidator->assert($postData);                
            } catch (Exception $ex) {
                $responseMessage = $ex->getMessage();
            }
            return $this->getBasePostDataAction($request, $this->model, null, $responseMessage);
        }else{
            return $this->getBaseGetDataAction($request, $this->model, null);
        }                            
    }    
    public function deleteAction(ServerRequest $request) {         
        return $this->deleteItemAction($request, $this->model);
    }  
}
