<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Controllers\Entitys;

use App\Controllers\BaseController;
use App\Models\Brand;
use App\Models\ModelVh;
use Exception;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Response\RedirectResponse;
use Illuminate\Database\Capsule\Manager as DB;
use Respect\Validation\Validator as v;
/**
 * Description of ModelController
 *
 * @author TpGimeno
 */
class ModelController extends BaseController
{
    protected $modelService;
    public function __construct(\App\Services\ModelService $modelService) {
        parent::__construct();
        $this->modelService = $modelService;
    }
    public function getIndexAction()
    {
        $models = DB::table('models')
                ->join('brands', 'brands.id', '=', 'models.brand_id')                
                ->select('models.id', 'brands.name as brand', 'models.name')
                ->whereNull('models.deleted_at')
                ->get();    
        $brands = Brand::All();
        return $this->renderHTML('/models/modelsList.html.twig', [
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
                ->orWhere("brand_id", "like", "%".$searchString."%")                
                ->get();       
        $brands = Brand::All();       
        return $this->renderHTML('/models/modelsList.html.twig', [
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
                $model = new ModelVh();
                $model->name = $postData['name'];
                $brand_id = Brand::Where("name", "=", $postData['brand'])->first()['id'];                
                $model->brand_id = $brand_id; 
                $model->save();     
                $responseMessage = 'Saved';     
            }
            catch(Exception $e)
            {                 
                $responseMessage = $this->errorService->getError($e);                
            }               
        }
        $modelSelected = null;
        if($request->getQueryParams('id'))
        {
            $modelSelected = ModelVh::find($request->getQueryParams('id'));
        }
        $brands = Brand::All();
        return $this->renderHTML('/models/modelsForm.html.twig', [
            'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
            'responseMessage' => $responseMessage,
            'model' => $modelSelected,
            'brands' => $brands
        ]);
    }

    public function deleteAction(ServerRequest $request)
    {       
        
        $this->modelService->deleteModel($request->getQueryParams('id'));               
        return new RedirectResponse('/intranet/vehicles/models/list');
    }

}
