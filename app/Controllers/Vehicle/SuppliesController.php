<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Controllers\Vehicle;

use App\Controllers\BaseController;
use App\Models\Mader;
use App\Models\Supplies;
use App\Reports\Vehicles\SuppliesReport;
use App\Services\Vehicle\SuppliesService;
use Exception;
use Illuminate\Database\Capsule\Manager as DB;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Diactoros\ServerRequest;
use Respect\Validation\Validator as v;
/**
 * Description of SuppliesController
 *
 * @author tonyl
 */
class SuppliesController extends BaseController {
    protected $suppliesService; 
    
    public function __construct(SuppliesService $suppliesService) {
        parent::__construct();
        $this->suppliesService = $suppliesService;
        $this->model = new Supplies();
        $this->route = 'vehicles/supplies';
        $this->titleList = 'Recambios';
        $this->titleForm = 'Recambio';
        $this->labels = $this->suppliesService->getLabelsArray(); 
        $this->itemsList = array('id', 'ref', 'name', 'pvp');
        $this->properties = $this->suppliesService->getModelProperties($this->model);
    }
    public function getIndexAction($request) {
        return $this->getBaseIndexAction($request, $this->model, null);
    }  
    
    public function getSuppliesDataAction($request) {
        $responseMessage = null; 
        $maders = $this->suppliesService->getAllRegisters(new Mader());
        $iterables = ['mader_id' => $maders];
        if($request->getMethod() == 'POST') {
            $postData = $request->getParsedBody();
            $suppliesValidator = v::key('ref', v::stringType()->notEmpty())
                    ->key('maderCode', v::stringType())
                    ->key('name', v::stringType()->notEmpty());
            try{
                $suppliesValidator->assert($postData); 
            } catch (Exception $ex) {
                $responseMessage = $ex->getMessage();
            }
            return $this->getBasePostDataAction($request, $this->model, $iterables, $responseMessage);
        }else{
            return $this->getBaseGetDataAction($request, $this->model, $iterables);
        }
    }    
    public function deleteAction(ServerRequest $request) {         
        $this->suppliesService->deleteRegister(new Supplies(), $request->getQueryParams('id'));           
        return new RedirectResponse('/Intranet/vehicles/supplies/list');
    }    
    public function getSuppliesReportAction(){
        $supplies = DB::table('supplies')  
                ->join('maders', 'supplies.mader', '=', 'maders.id')               
                ->select('supplies.id', 'supplies.name', 'supplies.ref', 'maders.name as mader', 'supplies.pvc', 'supplies.pvp' )               
                ->whereNull('supplies.deleted_at')                
                ->get()->toArray();        
        $newPostData = array_merge(['supplies' => $supplies ]);
        $report = new SuppliesReport();
        $report->AddPage();
        $report->Body($newPostData);        
        $report->Output();
    }
}
