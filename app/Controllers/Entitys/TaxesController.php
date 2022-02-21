<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Controllers\Entitys;

use App\Controllers\BaseController;
use App\Models\Taxes;
use App\Services\Entitys\TaxesService;
use Exception;
use Illuminate\Database\Capsule\Manager as DB;
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
    public function __construct(TaxesService $taxesService) {
        parent::__construct();
        $this->TaxesService = $taxesService;
    }
    public function getIndexAction() {
        $taxes = Taxes::all();        
        return $this->renderHTML('/Entitys/taxes/taxesList.html.twig', [
            'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
            'taxes' => $taxes            
        ]);
    }
    public function searchTaxesAction($request)
    {
        $searchData = $request->getParsedBody();        
        $searchString = $searchData['searchFilter']; 
        if($searchString){
            $taxes = DB::table('taxes')                            
                ->where('taxes.name', 'like', "%".$searchString."%")
                ->orWhere('taxes.percentaje', 'like', "%".$searchString."%") 
                ->whereNull('taxes.deleted_at')
                ->get();
        }
        else{
             $taxes = DB::table('taxes')                            
                ->select('taxes.id', 'taxes.name', 'taxes.percentaje')
                ->whereNull('taxes.deleted_at')
                ->get();
        }        
        return $this->renderHTML('/stores/taxesList.html.twig', [
            'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
            'taxes' => $taxes                    
        ]);
    }
    public function validateData($postData) {
        $responseMessage = null;           
        $taxesValidator = v::key('name', v::stringType()->notEmpty());        
        try{
            $taxesValidator->assert($postData); // true 
        }catch(Exception $e){                
            $responseMessage = $e->getMessage();
        }
        return $responseMessage;
    }
    public function getTaxesDataAction($request) {        
        $responseMessage = null;
        if($request->getMethod() == 'POST')
        {
            $postData = $request->getParsedBody();
            if($postData){
                $responseMessage = $this->validateData($postData);
            }            
            if($postData){
                $responseMessage = $this->saveAction($postData);
            }                      
        }        
        $taxesSelected = $this->renderSelected($request);
        return $this->renderHTML('/Entitys/taxes/taxesForm.html.twig', [
        'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
        'responseMessage' => $responseMessage,
        'taxes' => $taxesSelected
        ]);        
    }
    public function renderSelected($request){
        $taxesSelected = null;       
        if($request->getQueryParams('id'))
        {
            $taxesSelected = Taxes::find($request->getQueryParams('id'))->first();
        }
        return $taxesSelected;        
    }
           
    public function saveAction($postData){
        try{
                $taxes = new Taxes();
                $taxes->name = $postData['name'];               
                $taxes->percentaje = $postData['percentaje'];
                $taxes->save();     
                $responseMessage = 'Saved';
            }catch(Exception $e){                
                $responseMessage = $e->getMessage();
            }
        return $responseMessage;
    }
    public function deleteAction(ServerRequest $request)
    {       
        
        $this->TaxesService->deleteTaxes($request->getQueryParams('id'));               
        return new RedirectResponse('/Intranet/taxes/list');
    }

}

