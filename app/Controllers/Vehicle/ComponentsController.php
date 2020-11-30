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
                ->select('components.id', 'components.ref', 'maders.name as mader', 'components.name', 'components.serialNumber', 'components.pvc', 'components.pvp')
                ->whereNull('components.deleted_at')
                ->get();
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
        $componentsValidator = v::key('ref', v::stringType()->notEmpty())
                    ->key('serial_number', v::stringType()->notEmpty())
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
    public function addComponentData($postData)
    {        
        $component = new Components();                                          
        if(isset($postData['id']) && $postData['id'])
        {
            $component = Components::find($postData['id'])->first();                            
        }               
        $component->name = $postData['name'];
        $component->ref = $postData['ref'];   
        $component->serialNumber = $postData['serial_number'];
        $mader = Mader::where('name', 'like', "%".$postData['mader']."%")->first();               
        $component->mader = $mader->id;                
        $component->pvc = $this->tofloat($postData['pvc']);                
        $component->pvp = $this->tofloat($postData['pvp']);
        $component->observations = $postData['observations'];            
        return $component;
    }
    public function saveComponent($component)
    {        
        try{
            if(Components::find($component->id))
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
        return new RedirectResponse('/intranet/buys/components/list');
    } 
}
