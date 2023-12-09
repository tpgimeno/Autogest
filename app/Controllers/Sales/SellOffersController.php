<?php


namespace App\Controllers\Sales;

use App\Controllers\BaseController;
use App\Models\Brand;
use App\Models\Components;
use App\Models\Customer;
use App\Models\CustomerTypes;
use App\Models\ModelVh;
use App\Models\PaymentWays;
use App\Models\SellOffer;
use App\Models\Supplies;
use App\Models\Taxes;
use App\Models\Works;
use App\Services\Sales\SellOfferService;
use Laminas\Diactoros\Response\JsonResponse;
use Respect\Validation\Validator as v;
use ZipStream\Exception;
/**
 * Description of SellOffersController
 *
 * @author tonyl
 */
class SellOffersController extends BaseController {    
    protected $sellOfferService; 
    
    public function __construct(SellOfferService $sellOfferService){
        parent::__construct();
        $this->sellOfferService = $sellOfferService;
        $this->model = new SellOffer();
        $this->route = 'sales/offers';
        $this->titleList = 'Ofertas de Venta';
        $this->titleForm = 'Oferta de Venta';
        $this->labels = $this->sellOfferService->getLabelsArray(); 
        $this->itemsList = array('id', 'offerNumber', 'offerDate', 'customer', 'plate', 'brand', 'model', 'total');
        $this->properties = $this->sellOfferService->getModelProperties($this->model);
    }
//    Funcion que muestra la lista de ofertas
    public function getIndexAction($request){
        $values = $this->sellOfferService->list();
        return $this->getBaseIndexAction($request, $this->model, $values);
    }    
    public function getSellOffersDataAction($request){
        $responseMessage = null;    
        $iterables = [
            'vehicles' => $this->sellOfferService->getSellOfferVehicles(),
            'brands' => $this->sellOfferService->getAllRegisters(new Brand()),
            'models' => $this->sellOfferService->getAllRegisters(new ModelVh()),
            'taxes_id' => $this->sellOfferService->getAllRegisters(new Taxes()),
            'paymentWay_id' => $this->sellOfferService->getAllRegisters(new PaymentWays()),
            'customer_id' => $this->sellOfferService->getAllRegisters(new Customer()),
            'customerType' => $this->sellOfferService->getAllRegisters(new CustomerTypes()),
            'components' => $this->sellOfferService->getAllRegisters(new Components()),
            'supplies' => $this->sellOfferService->getAllRegisters(new Supplies()),
            'works' => $this->sellOfferService->getAllRegisters(new Works()),
            'vehicle_components' => $this->sellOfferService->getSellOfferComponents($request),
            'vehicle_supplies' => $this->sellOfferService->getSellOfferSupplies($request),
            'vehicle_works' => $this->sellOfferService->getSellOfferWorks($request)
        ];
//        var_dump($request->getQueryParams());
//        var_dump($iterables['vehicle_works']);die();
        if($request->getMethod() == 'POST'){
            $postData = $request->getParsedBody();             
            $offerValidator = v::key('offerNumber', v::stringType()->notEmpty())
                    ->key('plate', v::notEmpty())
                    ->key('customer_id', v::notEmpty());
            try{
                 $offerValidator->assert($postData);
            } 
            catch (Exception $ex){
                $responseMessage = $ex->getMessage();
            }
            
            return $this->getBasePostDataAction($request, $this->model, $iterables, $responseMessage);       
        }else{
            return $this->getBaseGetDataAction($request, $this->model, $iterables);
        }
    }
    
    public function getSellOffersNumberAction(){
        $template = "OV2023";
        $new_offer_number = null;
        $lastNumber = $this->sellOfferService->getLastOfferNumber()->offerNumber;
        if(!$lastNumber){
            $lastNumber = 1;
            $new_offer_number = $template . "0000" . $lastNumber;
        }else{
            $offset = strrpos($lastNumber, "0");
            $prenumber_last_offer = substr($lastNumber, 0, $offset);            
            $number_last_offer = intval(substr($lastNumber, $offset, strlen($lastNumber))) + 1;
            if(strlen($prenumber_last_offer) > 8){
                $new_offer_number = $prenumber_last_offer . strval($number_last_offer);
            }else{
                for($i=strlen($prenumber_last_offer);$i<10;$i++){
                    $prenumber_last_offer[$i] = 0;
                }
                $new_offer_number = $prenumber_last_offer . strval($number_last_offer);
            }
            
        }
        $response = new JsonResponse($new_offer_number);
        return $response;
    }
    
    public function getSellOffersModelsbyBrand($request){
        $postData = $request->getParsedBody();
        $models = $this->sellOfferService->getModelsByBrandAjax($postData['brand']);        
        $response = new JsonResponse($models);
        return $response;
    }
    
    public function getSellOffersVehiclesByModel($request){
        $postData = $request->getParsedBody();        
        $vehicles = $this->sellOfferService->getVehiclesByModelAjax($postData['brand'], $postData['model']);        
        $response = new JsonResponse($vehicles);
        return $response;
    }
    
    public function addComponentsSellOffersAction($request){
        $postData = $request->getParsedBody();        
        $responseMessage = $this->sellOfferService->saveSellOfferComponentAjax($postData);
        $response = new JsonResponse($responseMessage);
        return $response;
    }
    
    public function getComponentsSellOffersAction($request){       
        $components = $this->sellOfferService->getSellOfferComponents($request);
//        var_dump($components);die();
        $response = new JsonResponse($components);
        return $response;
    }
    
    public function delComponentsSellOffersAction($request){        
        $postData = $request->getParsedBody();        
        $component = $this->sellOfferService->deleteSellOfferComponentAjax($postData);
        $response = new JsonResponse($component);
        return $response;
    }
    
    public function addSuppliesSellOffersAction($request){
        $postData = $request->getParsedBody();        
        $responseMessage = $this->sellOfferService->saveSellOfferSupplyAjax($postData);
        $response = new JsonResponse($responseMessage);
        return $response;
    }
    
    public function getSuppliesSellOffersAction($request){       
        $supplies = $this->sellOfferService->getSellOfferSupplies($request);
//        var_dump($components);die();
        $response = new JsonResponse($supplies);
        return $response;
    }
    
    public function delSuppliesSellOffersAction($request){        
        $postData = $request->getParsedBody();        
        $supply = $this->sellOfferService->deleteSellOfferSupplyAjax($postData);
        $response = new JsonResponse($supply);
        return $response;
    }
    
    public function addWorksSellOffersAction($request){
        $postData = $request->getParsedBody();        
        $responseMessage = $this->sellOfferService->saveSellOfferWorkAjax($postData);
        $response = new JsonResponse($responseMessage);
        return $response;
    }
    
    public function getWorksSellOffersAction($request){       
        $works = $this->sellOfferService->getSellOfferWorks($request);
//        var_dump($components);die();
        $response = new JsonResponse($works);
        return $response;
    }
    
    public function delWorksSellOffersAction($request){        
        $postData = $request->getParsedBody();        
        $work = $this->sellOfferService->deleteSellOfferWorkAjax($postData);
        $response = new JsonResponse($work);
        return $response;
    }
    
}
