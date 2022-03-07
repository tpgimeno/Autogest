<?php

namespace App\Controllers\Sales;

use App\Controllers\BaseController;
use App\Models\Sellers;
use App\Services\Sells\SellersService;
use Exception;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Diactoros\ServerRequest;
use Respect\Validation\Validator as v;

class SellersController extends BaseController {    
    protected $sellersService;
    protected $list = '/Intranet/sellers/list';
    protected $tab = 'sales';
    protected $title = 'Comerciales';
    protected $save = "/Intranet/sellers/save";
    protected $formName = "sellersForm";
    protected $inputs = ['id' => ['id' => 'inputID', 'name' => 'id', 'title' => 'ID'],  
        'name' => ['id' => 'inputName', 'name' => 'name', 'title' => 'Nombre'],
        'fiscalId' => ['id' => 'inputFiscalId', 'name' => 'fiscalId', 'title' => 'NIF/CIF'],        
        'address' => ['id' => 'inputAdress', 'name' => 'address', 'title' => 'Dirección'],
        'city' => ['id' => 'inputCity', 'name' => 'city', 'title' => 'Población'],
        'postalCode' => ['id' => 'inputZip', 'name' => 'postalCode', 'title' => 'Código Postal'],        
        'state' => ['id' => 'inputState', 'name' => 'state', 'title' => 'Provincia'],
        'country' => ['id' => 'inputCountry', 'name' => 'country', 'title' => 'Pais'],
        'phone' => ['id' => 'inputPhone', 'name' => 'phone', 'title' => 'Telefono'],
        'email' => ['id' => 'inputEmail', 'name' => 'email', 'title' => 'Email'],
        'birthDate' => ['id' => 'inputBirthDate', 'name' => 'birthDate', 'title' => 'Fecha Nacimiento']];
    public function __construct(SellersService $sellersService) {
        parent::__construct();
        $this->sellersService = $sellersService;
    }   
    public function getIndexAction() {
        $sellers = $this->sellersService->getAllRegisters(new Sellers());
        return $this->renderHTML('/sells/sellers/sellersList.html.twig', [
            'list' => $this->list,
            'tab' => $this->tab,
            'title' => $this->title,
            'sellers' => $sellers
        ]);
    }    
    public function getSellersDataAction($request) {                
        $responseMessage = null;
        if($request->getMethod() == 'POST') {
            $postData = $request->getParsedBody();            
            $sellersValidator = v::key('name', v::stringType()->notEmpty()) 
            ->key('fiscalId', v::notEmpty())
            ->key('phone', v::notEmpty())
            ->key('email', v::stringType()->notEmpty());            
            try{
                $sellersValidator->assert($postData); // true 
                $responseMessage = $this->sellersService->saveRegister(new Sellers(), $postData);
            }catch(Exception $e){                
                $responseMessage = $e->getMessage();
            }              
        }
        $sellersSelected = $this->sellersService->setInstance(new Sellers(), $request->getQueryParams('id'));
        return $this->renderHTML('/sells/sellers/sellersForm.html.twig', [
            'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
            'responseMessage' => $responseMessage,
            'inputs' => $this->inputs,
            'save' => $this->save,
            'list' => $this->list,
            'formName' => $this->formName,
            'tab' => $this->tab,
            'title' => $this->title,
            'value' => $sellersSelected
        ]);
    }
    public function deleteAction(ServerRequest $request) {         
        $this->sellersService->deleteRegister(new Sellers(), $request->getQueryParams('id'));             
        return new RedirectResponse($this->list);
    }
}