<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Controllers\Vehicle;

use App\Controllers\BaseController;
use App\Models\Components;
use App\Models\Mader;
use App\Reports\Vehicles\ComponentsReport;
use App\Services\Vehicle\ComponentsService;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\QueryException;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Diactoros\ServerRequest;
use Respect\Validation\Validator as v;
use Symfony\Component\Config\Definition\Exception\Exception;

/**
 * Description of ComponentsController
 *
 * @author tonyl
 */
class ComponentsController extends BaseController
{
    protected $componentsService;    
    public function __construct(ComponentsService $componentsService) 
    {
        parent::__construct();
        $this->componentsService = $componentsService;
    }
    public function getIndexAction()
    {
        $components = DB::table('components')
                ->join('maders', 'components.mader', '=', 'maders.id')
                ->select('components.id', 'components.ref', 'maders.name as mader', 'components.name as name', 'components.serialNumber', 'components.pvc', 'components.pvp')
                ->whereNull('components.deleted_at')
                ->get();
//        var_dump($components);die();
        return $this->renderHTML('/vehicles/components/componentsList.html.twig', [
            'components' => $components
        ]);
    }    
    public function getComponentsDataAction($request)
    {       
        $responseMessage = null;        
        if($request->getMethod() == 'POST')
        {
            $postData = $request->getParsedBody();            
            if($this->validateData($postData))
            {
                $responseMessage = $this->validateData($postData);
            }
            $component = $this->addComponentData($postData);
            $responseMessage = $this->saveComponent($component);
        }                
        $params = $request->getQueryParams();
        $selected_component = $this->setComponent($params);        
        $maders = Mader::All();
        return $this->renderHTML('/vehicles/components/componentsForm.html.twig', [
            'component' => $selected_component,
            'maders' => $maders,
            'responseMessage' => $responseMessage
        ]);
    }
    public function validateData($postData)
    {
        $responseMessage = null;
//        var_dump($postData);die();
        $componentsValidator = v::key('ref', v::stringType()->notEmpty())
                    ->key('serialNumber', v::stringType()->notEmpty())
                    ->key('name', v::stringType()->notEmpty());
        try{
            $componentsValidator->assert($postData);               
        } catch (Exception $ex) {
            $responseMessage = $ex->getMessage();
        }
        return $responseMessage;
    }
    public function setComponent($params)
    {
        $selectedComponent = null;      
        if(isset($params['id']) && $params['id'])
        {        
            $selectedComponent = Components::find($params['id'])
                    ->join('maders', 'components.mader', '=', 'maders.id')
                    ->select('components.id', 'components.ref', 'components.serialNumber', 'maders.name', 'components.observations', 'components.pvc', 'components.pvp')
                    ->where('components.id', '=', $params['id'])
                    ->whereNull('components.deleted_at')
                    ->first();
        }
        return $selectedComponent;
    }
    public function addComponentData($postData){        
        $component = new Components();  
        $mader = null;
        if($this->findComponent($postData)){
            $component = Components::find(intval($postData['id']));            
        }               
        $component->name = $postData['name'];
        $component->ref = $postData['ref'];   
        $component->serialNumber = $postData['serialNumber'];
        if($this->findMader($postData)){
            $mader = Mader::where('name', 'like', "%".$postData['mader']."%")->first(); 
            $component->mader = $mader->id; 
        }   
        
        $component->pvc = $this->tofloat($postData['pvc']);                
        $component->pvp = $this->tofloat($postData['pvp']);
        $component->observations = $postData['observations'];   
        
        return $component;
    }
    public function findComponent($postData){
        $component = Components::find(intval($postData['id'])); 
        if($component){
            return true;
        }else{
            return false;
        }
    }
    public function findMader($postData){
        $mader = Mader::where('name', 'like', '%'.$postData['mader'].'%');
        if($mader){
            return true;
        }else{
            return false;
        }
    }
    public function saveComponent($component)
    {        
        try{
            $findComponent = Components::find($component->id);
            if($findComponent)
            {                
                $component->update();
                $responseMessage = 'Updated';
            }
            else
            {
                $component->save();
                $responseMessage = 'Saved';
            }
        } catch (QueryException $ex) {
            $responseMessage = $this->errorService->getError($ex);
        }
        return $responseMessage;
    }
    public function deleteAction(ServerRequest $request)
    {         
        $this->componentsService->deleteComponents($request->getQueryParams('id'));               
        return new RedirectResponse('/Intranet/buys/components/list');
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
