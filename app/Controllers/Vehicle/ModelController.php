<?php

namespace App\Controllers\Vehicle;


use App\Controllers\BaseController;
use App\Models\Brand;
use App\Models\ModelVh;
use App\Services\ErrorService;
use App\Services\Vehicle\ModelService;
use Laminas\Diactoros\ServerRequest;
use Respect\Validation\Validator as v;
/**
 * Description of ModelController
 *
 * @author TpGimeno
 */
class ModelController extends BaseController  {
    protected $modelService;
    protected $errorService;
    
    public function __construct(ModelService $modelService, ErrorService $errorService) {
        parent::__construct();
        $this->modelService = $modelService;
        $this->errorService = $errorService;
        $this->model = new ModelVh();
        $this->route = 'vehicles/models';
        $this->titleList = 'Modelos';
        $this->titleForm = 'Modelo';
        $this->labels = $this->modelService->getLabelsArray(); 
        $this->itemsList = array('id', 'brand', 'name');
        $this->properties = $this->modelService->getModelProperties($this->model);
    }
    public function getIndexAction($request) {       
        $values = $this->modelService->getModelItemsList();  
        return $this->getBaseIndexAction($request, $this->model, $values);
    }
   
    public function getModelDataAction($request) {                
        $responseMessage = null;
        $brands = $this->modelService->getAllRegisters(new Brand());
        $iterables = ['brand_id' => $brands];
        if($request->getMethod() == 'POST') {
            $postData = $request->getParsedBody();
            $modelValidator = v::key('name', v::stringType()->notEmpty());        
            try{
                $modelValidator->assert($postData); // true 
                
            }catch(Exception $e) {                 
                $responseMessage = $e->getMessage();                
            } 
            return $this->getBasePostDataAction($request, $this->model, $iterables, $responseMessage);
        }else{
            return $this->getBaseGetDataAction($request, $this->model, $iterables);
        }
    }    
    public function deleteAction(ServerRequest $request) {       
        return $this->deleteItemAction($request, $this->model);
    }
}
