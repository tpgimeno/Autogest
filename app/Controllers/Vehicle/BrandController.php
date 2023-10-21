<?php

namespace App\Controllers\Vehicle;

use App\Controllers\BaseController;
use App\Models\Brand;
use App\Services\ErrorService;
use App\Services\Vehicle\BrandService;
use Illuminate\Database\QueryException;
use Laminas\Diactoros\ServerRequest;
use Respect\Validation\Validator as v;
use Laminas\Diactoros\Response\RedirectResponse;
use ZipStream\Exception;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of BrandController
 *
 * @author tonyl
 */
class BrandController extends BaseController
{
    protected $brandService;
    protected $errorService;
   
    public function __construct(BrandService $brandService, ErrorService $errorService) {
        parent::__construct();
        $this->brandService = $brandService;
        $this->errorService = $errorService;
        $this->model = new Brand();
        $this->route = 'vehicles/brands';
        $this->titleList = 'Marcas';
        $this->titleForm = 'Marca';
        $this->labels = $this->brandService->getLabelsArray(); 
        $this->itemsList = array('id', 'name');
        $this->properties = $this->brandService->getModelProperties($this->model);
    }
    public function getIndexAction($request){
        return $this->getBaseIndexAction($request, $this->model, null);
    }    
    public function getBrandDataAction($request) {                
        $responseMessage = null;
        if($request->getMethod() === 'POST') {
            $postData = $request->getParsedBody();            
            $brandValidator = v::key('name', v::stringType()->notEmpty());                       
            try{
                $brandValidator->assert($postData); // true                 
            }catch(Exception $e){                
                $responseMessage = $e->getMessage();
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
