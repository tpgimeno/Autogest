<?php
namespace App\Controllers\Vehicle;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use App\Controllers\BaseController;
use App\Models\Components;
use App\Models\Mader;
use App\Reports\Vehicles\ComponentsReport;
use App\Services\Vehicle\ComponentsService;
use Exception;
use Laminas\Diactoros\ServerRequest;
use Respect\Validation\Validator as v;

/**
 * Description of ComponentsController
 *
 * @author tonyl
 */
class ComponentsController extends BaseController {
    protected $componentsService;    
    
    public function __construct(ComponentsService $componentsService) {
        parent::__construct();
        $this->componentsService = $componentsService;
        $this->model = new Components();
        $this->route = 'vehicles/components';
        $this->titleList = 'Componentes';
        $this->titleForm = 'Componente';
        $this->labels = $this->componentsService->getLabelsArray(); 
        $this->itemsList = array('id', 'ref', 'name', 'pvp');
        $this->properties = $this->componentsService->getModelProperties($this->model);
    }
    public function getIndexAction($request) {
        return $this->getBaseIndexAction($request, $this->model, null);
    }  
    
    public function getComponentsDataAction($request) {       
        $responseMessage = null;   
        $maders = $this->componentsService->getAllRegisters(new Mader());
        
        $iterables = ['mader_id' => $maders];
        if($request->getMethod() == 'POST') {
            $postData = $request->getParsedBody();            
            $componentsValidator = v::key('ref', v::stringType()->notEmpty())
                    ->key('serialNumber', v::stringType()->notEmpty())
                    ->key('name', v::stringType()->notEmpty());            
            try{
                $componentsValidator->assert($postData);  
                
            } catch (Exception $ex) {
                $responseMessage = $ex->getMessage();
            }
            return $this->getBasePostDataAction($request, $this->model, $iterables, $responseMessage);
        }else{
            return $this->getBaseGetDataAction($request, $this->model, $iterables);
        }
    }    
    public function deleteAction(ServerRequest $request) {         
        return $this->deleteItemAction($request, $this->model);
    } 
    public function getComponentsReportAction(){
        $components = DB::table('components')  
                ->join('maders', 'components.mader', '=', 'maders.id')               
                ->select('components.id', 'components.name', 'components.ref', 'maders.name as mader', 'components.pvc', 'components.pvp' )               
                ->whereNull('components.deleted_at')                
                ->get()->toArray();
        
        $newPostData = array_merge(['components' => $components ]);
        $report = new ComponentsReport();
        $report->AddPage();
        $report->Body($newPostData);        
        $report->Output();
    }
}
