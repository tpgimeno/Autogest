<?php

namespace App\Controllers\Garages;

use App\Controllers\BaseController;
use Respect\Validation\Validator as v;
use App\Models\Garage;
use App\Services\Buys\GarageService;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Response\RedirectResponse;

class GaragesController extends BaseController {     
    protected $garageService;
    protected $list = '/Intranet/garages/list';
    protected $tab = 'buys';
    protected $title = 'Talleres';
    protected $save = "/Intranet/garages/save";
    protected $formName = "garagesForm";
    protected $inputs = ['id' => ['id' => 'inputID', 'name' => 'id', 'title' => 'ID'], 
        'name' => ['id' => 'inputName', 'name' => 'name', 'title' => 'Nombre'],
        'fiscalId' => ['id' => 'inputFiscalId', 'name' => 'fiscalId', 'title' => 'NIF/CIF'],
        'fiscalName' => ['id' => 'inputFiscalName', 'name' => 'fiscalName', 'title' => 'Razón Social'],
        'address' => ['id' => 'inputAdress', 'name' => 'address', 'title' => 'Dirección'],
        'city' => ['id' => 'inputCity', 'name' => 'city', 'title' => 'Población'],
        'postalCode' => ['id' => 'inputZip', 'name' => 'postalCode', 'title' => 'Código Postal'],        
        'state' => ['id' => 'inputState', 'name' => 'state', 'title' => 'Provincia'],
        'country' => ['id' => 'inputCountry', 'name' => 'country', 'title' => 'Pais'],
        'phone' => ['id' => 'inputPhone', 'name' => 'phone', 'title' => 'Telefono'],
        'email' => ['id' => 'inputEmail', 'name' => 'email', 'title' => 'Email'],
        'site' => ['id' => 'inputWeb', 'name' => 'site', 'title' => 'Página Web']];
    public function __construct(GarageService $garageService) {
        parent::__construct();
        $this->garageService = $garageService;
    }     
    public function getIndexAction() {
        $garages = $this->garageService->getAllRegisters(new Garage());
        return $this->renderHTML('/buys/garages/garagesList.html.twig', [
            'list' => $this->list,
            'tab' => $this->tab,
            'title' => $this->title,
            'garages' => $garages
        ]);
    }      
    public function searchGarageAction($request) {
        $postData = $request->getParsedBody();
        $searchString = $postData['searchFilter'];
        $garages = $this->garageService->searchGarage($searchString);
        return $this->renderHTML('/buys/garages/garagesList.html.twig', [
            'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
            'list' => $this->list,
            'tab' => $this->tab,
            'title' => $this->title,
            'garages' => $garages
        ]);
    }
    public function getGarageDataAction($request) {                
        $responseMessage = null;
        if($request->getMethod() == 'POST') {
            $postData = $request->getParsedBody();            
            $garageValidator = v::key('name', v::stringType()->notEmpty()) 
            ->key('fiscalId', v::notEmpty())
            ->key('phone', v::notEmpty())
            ->key('email', v::stringType()->notEmpty());            
            try{
                $garageValidator->assert($postData); // true 
                $responseMessage = $this->garageService->saveRegister(new Garage(), $postData);                   
            }catch(\Exception $e){                
                $responseMessage = $this->errorService->getError($e);
            }              
        }
        $garageSelected = $this->garageService->setInstance(new Garage(), $request->getQueryParams('id'));
        return $this->renderHTML('/buys/garages/garagesForm.html.twig', [
            'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
            'responseMessage' => $responseMessage,
            'inputs' => $this->inputs,
            'save' => $this->save,
            'list' => $this->list,
            'formName' => $this->formName,
            'tab' => $this->tab,
            'title' => $this->title,
            'value' => $garageSelected
        ]);
    }
    public function deleteAction(ServerRequest $request) {         
        $this->garageService->deleteRegister(new Garage(), $request->getQueryParams('id'));          
        return new RedirectResponse('/Intranet/garages/list');
    }
}