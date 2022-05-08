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
    protected $list = '/Intranet/vehicles/models/list';
    protected $tab = 'buys';
    protected $title = 'Modelos';
    protected $save = "/Intranet/vehicles/models/save";
    protected $formName = "modelsForm";
    protected $inputs = ['id' => ['id' => 'inputID', 'name' => 'id', 'title' => 'ID'],
        'name' => ['id' => 'inputName', 'name' => 'name', 'title' => 'Nombre'],
        'brandId' => ['id' => 'inputBrand', 'name' => 'brand', 'title' => 'Marca']];
    public function __construct(ModelService $modelService, ErrorService $errorService) {
        parent::__construct();
        $this->modelService = $modelService;
        $this->errorService = $errorService;
    }
    public function getIndexAction() {       
        $models = $this->modelService->getModels();
        return $this->renderHTML('/vehicles/models/modelsList.html.twig', [
            'currentUser' => $this->currentUser->getCurrentUserEmailAction(),
            'list' => $this->list,
            'title' => $this->title,
            'tab' => $this->tab,
            'models' => $models
        ]);
    }
    public function searchModelAction($request) {
        $searchData = $request->getParsedBody();        
        $searchString = $searchData['searchFilter'];        
        $models = $this->modelService->searchModels($searchString);      
        $brands = $this->modelService->getAllRegisters(new Brand());    
        return $this->renderHTML('/vehicles/models/modelsList.html.twig', [
            'currentUser' => $this->currenUser->getCurrentUserEmailAction(),
            'list' => $this->list,
            'title' => $this->title,
            'tab' => $this->tab,
            'models' => $models,
            'brands' => $brands                
        ]);
    }    
    public function getModelDataAction($request) {                
        $responseMessage = null;
        if($request->getMethod() == 'POST') {
            $postData = $request->getParsedBody();            
            $modelValidator = v::key('name', v::stringType()->notEmpty());        
            try{
                $modelValidator->assert($postData); // true 
                $postData['brand'] = $this->modelService->getBrandByName($postData['brand']);
                $responseMessage = $this->modelService->saveRegister(new ModelVh(), $postData);
            }catch(Exception $e) {                 
                $responseMessage = $e->getMessage();                
            }            
        }
        $modelSelected = $this->modelService->getModel($request->getQueryParams('id'));
        $brands = $this->modelService->getBrands();
        return $this->renderHTML('/vehicles/models/modelsForm.html.twig', [
            'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
            'responseMessage' => $responseMessage,
            'list' => $this->list,
            'tab' => $this->tab,
            'title' => $this->title,
            'save' => $this->save,
            'formName' => $this->formName,
            'inputs' => $this->inputs,
            'value' => $modelSelected,
            'brands' => $brands
        ]);
    }    
    public function deleteAction(ServerRequest $request) {       
        $this->modelService->deleteRegister(new ModelVh(), $request->getQueryParams('id'));               
        return new RedirectResponse('/Intranet/vehicles/models/list');
    }
}
