<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Controllers\Buys;

use App\Controllers\BaseController;
use App\Models\Components;
use App\Models\Mader;
use App\Services\Buys\ComponentsService;
use Illuminate\Database\Capsule\Manager as DB;
use Laminas\Diactoros\ServerRequest;
use Respect\Validation\Validator as v;
use Symfony\Component\Config\Definition\Exception\Exception;
use Laminas\Diactoros\Response\RedirectResponse;

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
                ->select('components.id', 'components.ref', 'maders.name as mader', 'components.name', 'components.serial_number', 'components.pvc', 'components.pvp')
                ->whereNull('components.deleted_at')
                ->get();
        return $this->renderHTML('/buys/componentsList.html.twig', [
            'components' => $components
        ]);
    }    
    public function getComponentsDataAction($request)
    {       
        $responseMessage = null;
        $component_temp = null;
        $mader = null;
        if($request->getMethod() == 'POST')
        {
            $postData = $request->getParsedBody();
            
            $componentsValidator = v::key('ref', v::stringType()->notEmpty())
                    ->key('serial_number', v::stringType()->notEmpty())
                    ->key('name', v::stringType()->notEmpty());
            try{
                $componentsValidator->assert($postData);
                $component = new Components();
                $component_temp = null;                               
                if(Components::find($postData['id']))
                {
                    $component_temp = Components::find($postData['id']);                    
                    $component = $component_temp;                    
                }               
                $component->name = $postData['name'];
                $component->ref = $postData['ref'];   
                $component->serial_number = $postData['serial_number'];
                $mader = Mader::where('name', 'like', "%".$postData['mader']."%")->first();               
                $component->mader = $mader->id;                
                $component->pvc = $this->tofloat($postData['pvc']);                
                $component->pvp = $this->tofloat($postData['pvp']);
                $component->observations = $postData['observations'];
                if(isset($postData['accesories']))
                {
                    $component->accesories = $postData['accesories'];
                }
                else
                {
                    $component->accesories = null;
                }
                if($component_temp)
                {
                    $component->update();
                    $responseMessage = 'Updated';
                }
                else
                {
                    $component->save();
                    $responseMessage = 'Saved';
                }
                
            } catch (Exception $ex) {
                $responseMessage = $ex->getMessage();
            }
        }
        $selected_component = null;
        $maders = Mader::All();
        $params = $request->getQueryParams();
        if($request->getQueryParams('id'))
        {        
            $selected_component = Components::find($params['id'])
                    ->join('maders', 'components.mader', '=', 'maders.id')
                    ->select('components.id', 'components.ref', 'components.serial_number', 'maders.name', 'components.observations', 'components.pvc', 'components.pvp')
                    ->where('components.id', '=', $params['id'])
                    ->whereNull('components.deleted_at')
                    ->first();
        }
        return $this->renderHTML('/buys/componentsForm.html.twig', [
            'component' => $selected_component,
            'maders' => $maders,
            'responseMessage' => $responseMessage
        ]);
    } 
    public function deleteAction(ServerRequest $request)
    {         
        $this->componentsService->deleteComponents($request->getQueryParams('id'));               
        return new RedirectResponse('/intranet/buys/components/list');
    } 
}
