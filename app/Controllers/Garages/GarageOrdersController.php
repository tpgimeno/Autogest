<?php

namespace App\Controllers\Garages;

use App\Controllers\BaseController;
use App\Models\Brand;
use App\Models\Components;
use App\Models\Customer;
use App\Models\GarageOrder;
use App\Models\ModelVh;
use App\Models\Supplies;
use App\Models\Works;
use App\Services\Garages\GarageOrderService;
use Exception;
use Laminas\Diactoros\Response\JsonResponse;
use Respect\Validation\Validator as v;

class GarageOrdersController extends BaseController
{    
    
    public function __construct(GarageOrderService $GarageOrderService)
    {
        parent::__construct();
        $this->GarageOrderService = $GarageOrderService; 
        $this->model = new GarageOrder();
        $this->route = 'garageOrders';
        $this->titleList = 'Ordenes de Trabajo';
        $this->titleForm = 'Orden de Trabajo';
        $this->labels = $this->GarageOrderService->getLabelsArray();
        $this->itemsList = array('id', 'customer', 'brand', 'model', 'plate', 'phone', 'progres');
        $this->properties = $this->GarageOrderService->getModelProperties($this->model);
    }
    
    public function getIndexAction($request) {
        return $this->getBaseIndexAction($request, $this->model, null);
    }    
    
    public function getOrderDataAction($request) {                
        $responseMessage = null;
        $iterables = [
            'customer_id' => $this->GarageOrderService->getAllRegisters(new Customer()),
            'vehicles' => $this->GarageOrderService->getOrderVehicles(),
            'brands' => $this->GarageOrderService->getAllRegisters(new Brand()),
            'models' => $this->GarageOrderService->getAllRegisters(new ModelVh()),
            'components' => $this->GarageOrderService->getAllRegisters(new Components()),
            'supplies' => $this->GarageOrderService->getAllRegisters(new Supplies()),
            'works' => $this->GarageOrderService->getAllRegisters(new Works()),
            'vehicle_component_labels' => ['garageOrdercomponent_id' => 'garageOrdercomponent_id','mader' => 'mader','ref' => 'ref','name' => 'name','cantity' => 'cantity','pvp' => 'pvp','total' => 'total'],
            'vehicle_supply_labels' => ['garageOrdersupply_id' => 'garageOrdersupply_id','mader' => 'mader','ref' => 'ref','name' => 'name','cantity' => 'cantity','pvp' => 'pvp','total' => 'total'],
            'vehicle_work_labels' => ['garageOrderwork_id' => 'garageOrderwork_id','ref' => 'ref','name' => 'name','cantity' => 'cantity','pvp' => 'pvp','total' => 'total'],
            'component_functions' => ['set' => 'setComponent', 'delete' => 'delgarageOrderComponent'],
            'supply_functions' => ['set' => 'setSupply', 'delete' => 'delgarageOrderSupply'],
            'work_functions' => ['set' => 'setWork', 'delete' => 'delgarageOrderWork'],
            'assets_prices' => ['1' => 'baseComponents','2' => 'Base Componentes','3' => 'tvaComponents','4' => 'Iva','5' => 'totalComponents', '6' => 'Total Componentes'],
            'assets_labels' => ['id' => 'id', 'ref' => 'ref','name' => 'name','pvp' => 'pvp'],
            'setComponentsUrl' => "Intranet/garageOrders/components/set",
//            'vehicle_components' => $this->GarageOrderService->getGarageOrderComponents($request),
//            'vehicle_supplies' => $this->GarageOrderService->getGarageOrderSupplies($request),
//            'vehicle_works' => $this->GarageOrderService->getGarageOrderWorks($request),
            'parent_id' => 'garageOrder_id',
            'object_id' => ['1' => 'component_id','2' => 'supply_id','3' => 'work_id'],
            'modals_functions' => ['setComponent' => 'setComponent','saveComponent' => 'saveGarageOrderComponent()','setSupply' => 'setSupply', 'saveSupply' => 'saveGarageOrderSupply()','setWork' => 'setWork','saveWork' => 'saveGarageOrderWork()'],
            'forms' => ['1' => 'garageOrder_component_form','2' => 'garageOrder_supply_form','3' => 'garageOrder_work_form']
        ];
        if($request->getMethod() == 'POST') {
            $postData = $request->getParsedBody();            
            $GarageOrderValidator = v::key('name', v::stringType()->notEmpty()) 
            ->key('fiscal_id', v::notEmpty())
            ->key('phone', v::notEmpty())
            ->key('email', v::stringType()->notEmpty());            
            try{
                 $GarageOrderValidator->assert($postData); // true                     
            }catch(Exception $e){                
                $responseMessage = $e->getMessage();
            }   
            return $this->getBasePostDataAction($request, $this->model, $iterables, $responseMessage);
        }else{
            return $this->getBaseGetDataAction($request, $this->model, $iterables);
        }        
    }
    
    public function getGarageOrdersNumberAction(){
        $template = "OR2023";
        $new_order_number = null;
        $lastNumber = $this->GarageOrderService->getLastOrderNumber()->orderNumber;
        if(!$lastNumber){
            $lastNumber = 1;
            $new_order_number = $template . "0000" . $lastNumber;
        }else{
            $offset = strrpos($lastNumber, "0");
            $prenumber_last_order = substr($lastNumber, 0, $offset);            
            $number_last_order = intval(substr($lastNumber, $offset, strlen($lastNumber))) + 1;
            if(strlen($prenumber_last_order) > 8){
                $new_order_number = $prenumber_last_order . strval($number_last_order);
            }else{
                for($i=strlen($prenumber_last_order);$i<10;$i++){
                    $prenumber_last_order[$i] = 0;
                }
                $new_order_number = $prenumber_last_order . strval($number_last_order);
            }
            
        }
        $response = new JsonResponse($new_order_number);
        return $response;
    }
    
    public function addComponentsGarageOrdersAction($request){
        $postData = $request->getParsedBody();        
        $responseMessage = $this->GarageOrderService->saveGarageOrderComponentAjax($postData);
        $response = new JsonResponse($responseMessage);
        return $response;
    }
    
    public function delComponentsGarageOrdersAction($request){        
        $postData = $request->getParsedBody();        
        $component = $this->GarageOrderService->deleteGarageOrderComponentAjax($postData);
        $response = new JsonResponse($component);
        return $response;
    }
    
    public function addSuppliesGarageOrdersAction($request){
        $postData = $request->getParsedBody();        
        $responseMessage = $this->GarageOrderService->saveGarageOrderSupplyAjax($postData);
        $response = new JsonResponse($responseMessage);
        return $response;
    }    
    
    public function delSuppliesGarageOrdersAction($request){        
        $postData = $request->getParsedBody();        
        $supply = $this->GarageOrderService->deleteGarageOrderSupplyAjax($postData);
        $response = new JsonResponse($supply);
        return $response;
    }
    
    public function addWorksGarageOrdersAction($request){
        $postData = $request->getParsedBody();        
        $responseMessage = $this->GarageOrderService->saveGarageOrderWorkAjax($postData);
        $response = new JsonResponse($responseMessage);
        return $response;
    }   
    
    public function delWorksGarageOrdersAction($request){        
        $postData = $request->getParsedBody();        
        $work = $this->GarageOrderService->deleteGarageOrderWorkAjax($postData);
        $response = new JsonResponse($work);
        return $response;
    }

    public function deleteAction($request){
        return $this->deleteItemAction($request, $this->model);
    }

   

}