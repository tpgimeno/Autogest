<?php

namespace App\Controllers\Entitys;

use App\Controllers\BaseController;
use App\Models\Taxes;
use App\Services\Entitys\TaxesService;
use Exception;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Diactoros\ServerRequest;
use Respect\Validation\Validator as v;
/**
 * Description of taxesController
 *
 * @author TpGimeno
 */
class TaxesController extends BaseController {    
    public function __construct(TaxesService $taxesService) {
        parent::__construct();
        $this->TaxesService = $taxesService;
        $this->model = new Taxes();
        $this->route = 'taxes';
        $this->titleList = 'Tipos de Iva';
        $this->titleForm = 'Tipo de Iva';
        $this->labels = $this->TaxesService->getLabelsArray(); 
        $this->itemsList = array('id', 'name', 'percentaje');
    }
    public function getIndexAction($request) {
       return $this->getBaseIndexAction($request, $this->model, null);
    }    
    public function getTaxesDataAction($request) {        
        $responseMessage = null;
        if($request->getMethod() == 'POST') {
            $postData = $request->getParsedBody();
            $taxesValidator = v::key('name', v::stringType()->notEmpty());        
            try {
                $taxesValidator->assert($postData);
            }catch(Exception $e){                
                $responseMessage = $e->getMessage();
            }
            return $this->getBasePostDataAction($request, $this->model, null, $responseMessage);
        }      
        return $this->getBaseGetDataAction($request, $this->model, null);      
    }         
    public function deleteAction(ServerRequest $request) { 
        $params = $request->getQueryParams();
        $this->TaxesService->deleteRegister(new Taxes(), $params);            
        return new RedirectResponse('/Intranet/taxes/list?menu=' . $params['menu'] . '&item=' . $params['item']);
    }
}

