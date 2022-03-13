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
    protected $list = "/Intranet/vehicles/supplies/list";
    protected $script = 'js/supplies.js';
    protected $tab = "buys";
    protected $title = "Recambios";
    protected $save = "/Intranet/vehicles/supplies/save";
    protected $formName = 'suppliesForm';  
    protected $search = '/Intranet/vehicles/supplies/search';    
    protected $inputs = ['id' => ['id' => 'inputId', 'name' => 'id', 'title' => 'ID'],
        'name' => ['id' => 'inputName', 'name' => 'name', 'title' => 'Nombre'],
        'ref' => ['id' => 'inputRef', 'name' => 'ref', 'title' => 'Referencia'],
        'selectMader' => ['id' => 'inputMader', 'name' => 'mader', 'title' => 'Fabricante'],
        'maderCode' => ['id' => 'inputMaderCode', 'name' => 'maderCode', 'title' => 'Referencia Fabricante'],       
        'observations' => ['id' => 'observations', 'name' => 'observations', 'title' => 'Observaciones'],
        'stock' => ['id' => 'inputStock', 'name' => 'stock', 'title' => 'Stock'],
        'pvc' => ['id' => 'inputPvc', 'name' => 'pvc', 'title' => 'Precio Compra'],
        'pvp' => ['id' => 'inputPvp', 'name' => 'pvp', 'title' => 'Precio Venta'],
        'tvaBuy' => ['id' => 'inputTvaBuy', 'name' => 'tvaBuy', 'title' => 'Iva Compra'],
        'tvaSell' => ['id' => 'inputTvaSell', 'name' => 'tvaSell', 'title' => 'Iva Venta'],
        'totalBuy' => ['id' => 'inputTotalBuy', 'name' => 'totalBuy', 'title' => 'Total Compra'],
        'totalSell' => ['id' => 'inputTotalSell', 'name' => 'totalSell', 'title' => 'Total Venta']];
    public function __construct(SuppliesService $suppliesService) {
        parent::__construct();
        $this->suppliesService = $suppliesService;
    }
    public function getIndexAction() {
        $supplies = $this->suppliesService->getSupplies();       
        return $this->renderHTML('/vehicles/supplies/suppliesList.html.twig', [
            'title' => $this->title,
            'list' => $this->list,
            'tab' => $this->tab,
            'search' => $this->search,
            'supplies' => $supplies
        ]);
    }  
    public function searchSupplyAction($request) {
        $searchData = $request->getParsedBody();      
        $supplies = $this->suppliesService->searchSupplies($searchData['searchFilter']);
        return $this->renderHTML('/vehicles/supplies/suppliesList.html.twig', [
            'title' => $this->title,
            'list' => $this->list,
            'tab' => $this->tab,
            'search' => $this->search,
            'supplies' => $supplies
        ]);
    }    
    public function getSuppliesDataAction($request) {
        $responseMessage = null;        
        if($request->getMethod() == 'POST') {
            $postData = $request->getParsedBody();
            $suppliesValidator = v::key('ref', v::stringType()->notEmpty())
                    ->key('maderCode', v::stringType())
                    ->key('name', v::stringType()->notEmpty());
            try{
                $suppliesValidator->assert($postData); 
                $postData['mader'] = $this->suppliesService->getMaderByName($postData);               
                $responseMessage = $this->suppliesService->saveRegister(new Supplies(), $postData);
            } catch (Exception $ex) {
                $responseMessage = $ex->getMessage();
            }
        }        
        $maders = $this->suppliesService->getMaders();
        $selected_supply = $this->suppliesService->setSupplyInstance($request->getQueryParams('id'));    
        return $this->renderHTML('/vehicles/supplies/suppliesForm.html.twig', [
            'value' => $selected_supply,
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
