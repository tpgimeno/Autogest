<?php

namespace App\Controllers\Vehicle;

use App\Controllers\BaseController;
use App\Models\Brand;
use App\Services\ErrorService;
use App\Services\Vehicle\BrandService;
use Illuminate\Database\QueryException;
use Laminas\Diactoros\ServerRequest;
use Respect\Validation\Validator as v;
use Laminas\Diactoros\Response\RedirectResponse;
use ZipStream\Exception;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of BrandController
 *
 * @author tonyl
 */
class BrandController extends BaseController
{
    protected $brandService;
    protected $errorService;
    protected $list = '/Intranet/vehicles/brands/list';
    protected $tab = 'buys';
    protected $title = 'Marcas';
    protected $save = "/Intranet/vehicles/brands/save";
    protected $formName = "brandsForm";
    protected $inputs = ['id' => ['id' => 'inputID', 'name' => 'id', 'title' => 'ID'],
        'name' => ['id' => 'inputName', 'name' => 'name', 'title' => 'Nombre']];
    public function __construct(BrandService $brandService, ErrorService $errorService) {
        parent::__construct();
        $this->brandService = $brandService;
        $this->errorService = $errorService;
    }
    public function getIndexAction(){
        $brands = $this->brandService->getAllRegisters(new Brand());
        return $this->renderHTML('/vehicles/brands/brandsList.html.twig', [
            'currentUser' => $this->currentUser->getCurrentUserEmailAction(),
            'list' => $this->list,
            'title' => $this->title,
            'tab' => $this->tab,
            'brands' => $brands
        ]);
    }    
    public function getBrandDataAction($request) {                
        $responseMessage = null;
        if($request->getMethod() === 'POST')
        {
            $postData = $request->getParsedBody();            
            $brandValidator = v::key('name', v::stringType()->notEmpty());                       
            try{
                $brandValidator->assert($postData); // true 
                $responseMessage = $this->brandService->saveRegister(new Brand(), $postData);
            }catch(Exception $e){                
                $responseMessage = $e->getMessage();
            }            
        }
        $brandSelected = $this->brandService->setInstance(new Brand(), $request->getQueryParams('id'));     
        return $this->renderHTML('/vehicles/brands/brandsForm.html.twig', [
            'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
            'responseMessage' => $responseMessage,
            'list' => $this->list,
            'tab' => $this->tab,
            'title' => $this->title,
            'save' => $this->save,
            'formName' => $this->formName,
            'inputs' => $this->inputs,
            'value' => $brandSelected
        ]);
    }    
    public function deleteAction(ServerRequest $request) {         
        $this->brandService->deleteRegister(new Brand(), $request->getQueryParams('id'));            
        return new RedirectResponse('/Intranet/vehicles/brands/list');
    }

}
