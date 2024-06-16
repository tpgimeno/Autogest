<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace App\Controllers\Garages;

use App\Controllers\BaseController;
use App\Models\Brand;
use App\Models\Components;
use App\Models\Customer;
use App\Models\GarageOrder;
use App\Models\ModelVh;
use App\Models\Supplies;
use App\Models\Works;
use App\Models\WorkSheets;
use App\Services\Garages\WorkSheetsService;
use Laminas\Diactoros\Response\JsonResponse;
use Respect\Validation\Validator as v;

/**
 * Description of WorkSheetsController
 *
 * @author tonyl
 */
class WorkSheetsController extends BaseController{
    
    public function __construct(WorkSheetsService $WorkSheetsService)
    {
        parent::__construct();
        $this->WorkSheetsService = $WorkSheetsService; 
        $this->model = new WorkSheets();
        $this->route = 'workSheets';
        $this->titleList = 'Hojas de Trabajo';
        $this->titleForm = 'Hoja de Trabajo';
        $this->labels = $this->WorkSheetsService->getLabelsArray();
        $this->itemsList = array('id', 'customer', 'brand', 'model', 'work');
        $this->properties = $this->WorkSheetsService->getModelProperties($this->model);
    }
    
    public function getIndexAction($request) {        
        $values = $this->WorkSheetsService->list();        
        return $this->getBaseIndexAction($request, $this->model, $values);
    } 
    
    public function getWorkSheetsDataAction($request) {                
        $responseMessage = null;
        $iterables = [
            'customer_id' => $this->WorkSheetsService->getAllRegisters(new Customer()),
            'order_id' => $this->WorkSheetsService->getAllRegisters(new GarageOrder()),
            'vehicles' => $this->WorkSheetsService->getOrderVehicles(),
            'brands' => $this->WorkSheetsService->getAllRegisters(new Brand()),
            'models' => $this->WorkSheetsService->getAllRegisters(new ModelVh()),
            'components' => $this->WorkSheetsService->getAllRegisters(new Components()),
            'supplies' => $this->WorkSheetsService->getAllRegisters(new Supplies()),
            'works' => $this->WorkSheetsService->getAllRegisters(new Works()),
            'workId' => $this->WorkSheetsService->getAllRegisters(new Works()),
            'vehicle_component_labels' => ['workSheetscomponent_id' => 'workSheetscomponent_id','mader' => 'mader','ref' => 'ref','name' => 'name','cantity' => 'cantity','pvp' => 'pvp','total' => 'total'],
            'vehicle_supply_labels' => ['workSheetssupply_id' => 'workSheetssupply_id','mader' => 'mader','ref' => 'ref','name' => 'name','cantity' => 'cantity','pvp' => 'pvp','total' => 'total'],
            'vehicle_work_labels' => ['workSheetswork_id' => 'workSheetswork_id','ref' => 'ref','name' => 'name','cantity' => 'cantity','pvp' => 'pvp','total' => 'total'],
            'component_functions' => ['set' => 'setComponent', 'delete' => 'delWorkSheetsComponent'],
            'supply_functions' => ['set' => 'setSupply', 'delete' => 'delWorkSheetsSupply'],
            'work_functions' => ['set' => 'setWork', 'delete' => 'delWorkSheetsWork'],
            'assets_prices' => ['1' => 'baseComponents','2' => 'Base Componentes','3' => 'tvaComponents','4' => 'Iva','5' => 'totalComponents', '6' => 'Total Componentes','7' => 'baseSupplies', '8' => 'Base Recambios', '9' => 'tvaSupplies', '10' => 'Iva Recambios', '11' => 'totalSupplies', '12' => 'Total Recambios', '13' => 'baseWorks', '14' => 'Base Trabajos', '15' => 'tvaWorks', '16' => 'Iva Trabajos', '17' => 'totalWorks', '18' => 'Total Trabajos'],
            'assets_labels' => ['id' => 'id', 'ref' => 'ref','name' => 'name','pvp' => 'pvp'],
            'setComponentsUrl' => "Intranet/workSheetss/components/set",
            'vehicle_components' => $this->WorkSheetsService->getWorkSheetComponents($request),
            'vehicle_supplies' => $this->WorkSheetsService->getWorkSheetSupplies($request),
            'vehicle_works' => $this->WorkSheetsService->getWorkSheetWorks($request),
            'parent_id' => 'workSheet_id',
            'object_id' => ['1' => 'component_id','2' => 'supply_id','3' => 'work_id'],
            'modals_functions' => ['setComponent' => 'setComponent','saveComponent' => 'saveWorkSheetsComponent()','setSupply' => 'setSupply', 'saveSupply' => 'saveWorkSheetsSupply()','setWork' => 'setWork','saveWork' => 'saveWorkSheetsWork()'],
            'forms' => ['1' => 'workSheets_component_form','2' => 'workSheets_supply_form','3' => 'workSheets_work_form']
        ];
        if($request->getMethod() == 'POST') {
            $postData = $request->getParsedBody();            
            $WorkSheetsValidator = v::key('workSheetNumber', v::stringType()->notEmpty());            
            try{
                 $WorkSheetsValidator->assert($postData); // true                     
            }catch(Exception $e){                
                $responseMessage = $e->getMessage();
            }   
            return $this->getBasePostDataAction($request, $this->model, $iterables, $responseMessage);
        }else{
            return $this->getBaseGetDataAction($request, $this->model, $iterables);
        }        
    }
    
    public function getWorkSheetsNumberAction(){
        $template = "OT2023";
        $new_workSheet_number = null;
        $lastNumber = $this->WorkSheetsService->getLastWorkSheetNumber();        
        if(!$lastNumber){             
            $lastNumber = 1;
            $new_workSheet_number = $template . "0000" . $lastNumber;
        }else{
            $offset = strrpos($lastNumber->workSheetNumber, "0");
            $prenumber_last_workSheet = substr($lastNumber->workSheetNumber, 0, $offset);            
            $number_last_workSheet = intval(substr($lastNumber->workSheetNumber, $offset, strlen($lastNumber))) + 1;
            if(strlen($prenumber_last_workSheet) > 8){
                $new_workSheet_number = $prenumber_last_workSheet . strval($number_last_workSheet);
            }else{
                for($i=strlen($prenumber_last_workSheet);$i<10;$i++){
                    $prenumber_last_workSheet[$i] = 0;
                }
                $new_workSheet_number = $prenumber_last_workSheet . strval($number_last_workSheet);
            }            
        }
        $response = new JsonResponse($new_workSheet_number);
        
        return $response;
    }
    
    public function addComponentsWorkSheetsAction($request){
        $postData = $request->getParsedBody();     
       
        $responseMessage = $this->WorkSheetsService->addComponentsWorkSheetsAction($postData);
        $response = new JsonResponse($responseMessage);
        return $response;
    }
    
    public function delComponentsWorkSheetsAction($request){        
        $postData = $request->getParsedBody();        
        $component = $this->WorkSheetsService->delComponentsWorkSheetsAction($postData);
        $response = new JsonResponse($component);
        return $response;
    }
    
    public function addSuppliesWorkSheetsAction($request){
        $postData = $request->getParsedBody();   
        
        $responseMessage = $this->WorkSheetsService->addSuppliesWorkSheetsAction($postData);
        $response = new JsonResponse($responseMessage);
        return $response;
    }    
    
    public function delSuppliesWorkSheetsAction($request){        
        $postData = $request->getParsedBody();        
        $supply = $this->WorkSheetsService->delSuppliesWorkSheetsAction($postData);
        $response = new JsonResponse($supply);
        return $response;
    }
    
    public function addWorksWorkSheetsAction($request){
        $postData = $request->getParsedBody();        
        $responseMessage = $this->WorkSheetsService->addWorksWorkSheetsAction($postData);
        $response = new JsonResponse($responseMessage);
        return $response;
    }   
    
    public function delWorksWorkSheetsAction($request){        
        $postData = $request->getParsedBody();        
        $work = $this->WorkSheetsService->delWorksWorkSheetsAction($postData);
        $response = new JsonResponse($work);
        return $response;
    }

    public function deleteAction($request){
        return $this->deleteItemAction($request, $this->model);
    } 
}
