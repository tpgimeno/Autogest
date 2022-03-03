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
    protected $TaxesService;
    protected $list = '/Intranet/taxes/list';
    protected $tab = 'home';
    protected $title = 'Tipos de Iva';
    protected $save = "/Intranet/taxes/save";
    protected $formName = "taxesForm";
    protected $inputs = ['id' => ['id' => 'inputID', 'name' => 'id', 'title' => 'ID'],
        'name' => ['id' => 'inputName', 'name' => 'name', 'title' => 'Nombre'],
        'percentaje' => ['id' => 'inputPercentaje', 'name' => 'percentaje', 'title' => 'Porcentaje']];
    public function __construct(TaxesService $taxesService) {
        parent::__construct();
        $this->TaxesService = $taxesService;
    }
    public function getIndexAction() {
        $taxes = $this->TaxesService->getAllRegisters(new Taxes()); 
        return $this->renderHTML('/Entitys/taxes/taxesList.html.twig', [
            'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
            'list' => $this->list,
            'title' => $this->title,
            'tab' => $this->tab,
            'taxes' => $taxes            
        ]);
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
            $responseMessage = $this->TaxesService->saveRegister(new Taxes(), $postData);
        }      
        $taxesSelected = $this->TaxesService->setInstance(new Taxes(), $request->getQueryParams());
        return $this->renderHTML('/Entitys/taxes/taxesForm.html.twig', [
        'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
        'responseMessage' => $responseMessage,
        'list' => $this->list,
        'tab' => $this->tab,
        'title' => $this->title,
        'save' => $this->save,
        'formName' => $this->formName,
        'inputs' => $this->inputs,
        'value' => $taxesSelected
        ]);        
    }         
    public function deleteAction(ServerRequest $request) {        
        $this->TaxesService->deleteRegister(new Taxes(), $request->getQueryParams('id'));            
        return new RedirectResponse($this->list);
    }
}

