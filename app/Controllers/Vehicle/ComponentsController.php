<?php
namespace App\Controllers\Vehicle;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use App\Controllers\BaseController;
use App\Models\Components;
use App\Reports\Vehicles\ComponentsReport;
use App\Services\Vehicle\ComponentsService;
use Laminas\Diactoros\ServerRequest;
use Respect\Validation\Validator as v;
use Laminas\Diactoros\Response\RedirectResponse;

/**
 * Description of ComponentsController
 *
 * @author tonyl
 */
class ComponentsController extends BaseController {
    protected $componentsService;    
    protected $list = "/Intranet/vehicles/components/list";
    protected $tab = "buys";
    protected $title = "Componentes";
    protected $save = "/Intranet/vehicles/components/save";
    protected $formName = 'componentsForm';  
    protected $search = '/Intranet/vehicles/components/search';    
    protected $inputs = ['id' => ['id' => 'inputId', 'name' => 'id', 'title' => 'ID'], 
        'ref' => ['id' => 'inputRef', 'name' => 'ref', 'title' => 'Referencia'],
        'selectMader' => ['id' => 'inputMader', 'name' => 'mader', 'title' => 'Fabricante'],
        'serialNumber' => ['id' => 'inputSerialNumber', 'name' => 'serialNumber', 'title' => 'Numero de Serie'],
        'name' => ['id' => 'inputName', 'name' => 'name', 'title' => 'Nombre'],
        'observations' => ['id' => 'observations', 'name' => 'observations', 'title' => 'Observaciones'],
        'pvc' => ['id' => 'inputPvc', 'name' => 'pvc', 'title' => 'Precio Compra'],
        'pvp' => ['id' => 'inputPvp', 'name' => 'pvp', 'title' => 'Precio Venta'],
        'tvaBuy' => ['id' => 'inputTvaBuy', 'name' => 'tvaBuy', 'title' => 'Iva Compra'],
        'tvaSell' => ['id' => 'inputTvaSell', 'name' => 'tvaSell', 'title' => 'Iva Venta'],
        'totalBuy' => ['id' => 'inputTotalBuy', 'name' => 'totalBuy', 'title' => 'Total Compra'],
        'totalSell' => ['id' => 'inputTotalSell', 'name' => 'totalSell', 'title' => 'Total Venta']];
    protected $script = 'js/components.js';
    public function __construct(ComponentsService $componentsService) {
        parent::__construct();
        $this->componentsService = $componentsService;
    }
    public function getIndexAction() {
        $components = $this->componentsService->getComponents();
        $maders = $this->componentsService->getMaders();       
        return $this->renderHTML('/vehicles/components/componentsList.html.twig', [
            'components' => $components,
            'title' => $this->title,
            'list' => $this->list,
            'tab' => $this->tab,
            'search' => $this->search,
            'maders' => $maders            
        ]);
    }  
    public function searchComponentAction($request) {
        $searchData = $request->getParsedBody();      
        $components = $this->componentsService->searchSupplies($searchData['searchFilter']);
        return $this->renderHTML('/vehicles/components/componentsList.html.twig', [
            'title' => $this->title,
            'list' => $this->list,
            'tab' => $this->tab,
            'search' => $this->search,
            'components' => $components
        ]);
    }    
    public function getComponentsDataAction($request) {       
        $responseMessage = null;        
        if($request->getMethod() == 'POST') {
            $postData = $request->getParsedBody();            
            $componentsValidator = v::key('ref', v::stringType()->notEmpty())
                    ->key('serialNumber', v::stringType()->notEmpty())
                    ->key('name', v::stringType()->notEmpty());
            $postData['mader'] = $this->componentsService->getMaderByName($postData['mader']);
            $responseMessage = $this->componentsService->saveRegister(new Components(), $postData);
            try{
                $componentsValidator->assert($postData);  
                
            } catch (Exception $ex) {
                $responseMessage = $ex->getMessage();
            }            
        }
        $selected_component = $this->componentsService->setComponentInstance($request->getQueryParams('id'));
        $maders = $this->componentsService->getMaders(); 
//        var_dump($selected_component);die();
        return $this->renderHTML('/vehicles/components/componentsForm.html.twig', [
            'value' => $selected_component,
            'maders' => $maders,
            'title' => $this->title,
            'list' => $this->list,
            'tab' => $this->tab,
            'save' => $this->save,
            'formName' => $this->formName,
            'inputs' => $this->inputs,
            'script' => $this->script,
            'responseMessage' => $responseMessage
        ]);
    }    
    public function deleteAction(ServerRequest $request) {         
        $this->componentsService->deleteRegister(new Components(), $request->getQueryParams('id'));               
        return new RedirectResponse($this->list);
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
