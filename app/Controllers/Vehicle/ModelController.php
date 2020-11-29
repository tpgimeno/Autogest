<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Controllers\Vehicle;

use App\Controllers\BaseController;
use App\Models\Brand;
use App\Models\ModelVh;
use App\Services\ErrorService;
use App\Services\Vehicle\ModelService;
use Exception;
use Illuminate\Database\Capsule\Manager as DB;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Diactoros\ServerRequest;
use Respect\Validation\Validator as v;
/**
 * Description of ModelController
 *
 * @author TpGimeno
 */
class ModelController extends BaseController
{
    protected $modelService;
    protected $errorService;
    public function __construct(ModelService $modelService, ErrorService $errorService) {
        parent::__construct();
        $this->modelService = $modelService;
        $this->errorService = $errorService;
    }
    public function getIndexAction()
    {
        $models = DB::table('models')
                ->join('brands', 'brands.id', '=', 'models.brandId')                
                ->select('models.id', 'brands.name as brand', 'models.name')
                ->whereNull('models.deleted_at')
                ->get();    
        $brands = Brand::All();
        return $this->renderHTML('/vehicles/models/modelsList.html.twig', [
            'currentUser' => $this->currentUser->getCurrentUserEmailAction(),
            'models' => $models,
            'brands' => $brands
        ]);
    }
    public function searchModelAction($request)
    {
        $searchData = $request->getParsedBody();        
        $searchString = $searchData['searchFilter'];        
        $model = ModelVh::Where("name", "like", "%".$searchString."%")
                ->orWhere("brandId", "like", "%".$searchString."%")                
                ->get();       
        $brands = Brand::All();       
        return $this->renderHTML('/vehicles/models/modelsList.html.twig', [
            'currentUser' => $this->currenUser->getCurrentUserEmailAction(),
            'models' => $model,
            'brands' => $brands                
        ]);
    }    
    public function getModelDataAction($request)
    {                
        $responseMessage = null;
        if($request->getMethod() == 'POST')
        {
            $postData = $request->getParsedBody();            
            $modelValidator = v::key('name', v::stringType()->notEmpty());        
            try{
                $modelValidator->assert($postData); // true                                   
            }
            catch(Exception $e)
            {                 
                $responseMessage = $e->getMessage();                
            }
            $model = $this->addModel($postData);
            $responseMessage = $this->saveModel($model);
        }
        $modelSelected = $this->setModel($request);
        $brands = Brand::All();
        return $this->renderHTML('/vehicles/models/modelsForm.html.twig', [
            'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
            'responseMessage' => $responseMessage,
            'model' => $modelSelected,
            'brands' => $brands
        ]);
    }
    public function saveModel($model){
        try{
            if(ModelVh::find($model->id))
            {
                $model->update();
                $message = 'Updated';                
            }
            else
            {
                $model->save();
                $message = 'Saved';
            }
        } catch (QueryException $ex) {
            $message = $this->errorService->getError($ex);
        }
        return $message;
    }
    public function setModel($request){
        $modelSelected = null;
        $params = $request->getQueryParams();
        if(isset($params['id'])&& $params['id'])
        {
            $modelSelected = ModelVh::find($params['id']);
        }
        return $modelSelected;
    } 
    public function addModel($postData){
        $model = new ModelVh();
        if(isset($postData['id']) && $postData['id'])
        {
            $model = ModelVh::find($postData['id']);
        }
        $model->name = $postData['name'];
        $brand_id = Brand::Where("name", "=", $postData['brand'])->first()['id'];                
        $model->brandId = $brand_id;
        return $model;
    }
            
    public function deleteAction(ServerRequest $request)
    {       
        $this->modelService->deleteModel($request->getQueryParams('id'));               
        return new RedirectResponse('/intranet/vehicles/models/list');
    }

}
