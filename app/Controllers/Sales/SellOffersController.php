<?php


namespace App\Controllers\Sales;

use App\Controllers\BaseController;
use App\Models\Brand;
use App\Models\Components;
use App\Models\Customer;
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
            'taxes' => $this->sellOfferService->getAllRegisters(new Taxes()),
            'paymentWays' => $this->sellOfferService->getAllRegisters(new PaymentWays()),
            'customers' => $this->sellOfferService->getAllRegisters(new Customer()),
            'components' => $this->sellOfferService->getAllRegisters(new Components()),
            'supplies' => $this->sellOfferService->getAllRegisters(new Supplies()),
            'works' => $this->sellOfferService->getAllRegisters(new Works()),
            'sellOfferComponents' => $this->sellOfferService->getSellOfferComponents($request),
            'sellOfferSupplies' => $this->sellOfferService->getSellOfferSupplies($request),
            'sellOfferWorks' => $this->sellOfferService->getSellOfferWorks($request)
        ];
        if($request->getMethod() == 'POST'){
            $postData = $request->getParsedBody();            
            $offerValidator = v::key('offer_number', v::stringType()->notEmpty())
                    ->key('customer_id', v::notEmpty())
                    ->key('vehicle_id', v::notEmpty());
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
    
}
