<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Controllers\Vehicle;

use App\Controllers\BaseController;
use App\Models\Accesories;
use App\Services\ErrorService;
use App\Services\Vehicle\AccesoriesService;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\QueryException;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Diactoros\ServerRequest;
use Respect\Validation\Validator as v;
use ZipStream\Exception;



/**
 * Description of AccesoriesController
 *
 * @author tonyl
 */
class AccesoriesController extends BaseController
{
    protected $accesoriesService;
    protected $responseMessage;
    
    public function __construct(AccesoriesService $accesoriesService, ErrorService $errorService) {
        parent::__construct();
        $this->accesoriesService = $accesoriesService;
        $this->errorService = $errorService;
    }
    public function getIndexAction()
    {
        $accesories = DB::table('accesories')               
                ->select('accesories.id', 'accesories.name')
                ->get();        
        return $this->renderHTML('/vehicles/accesories/accesoriesList.html.twig', [
            'accesories' => $accesories
        ]);
    }    
    public function getAccesoryDataAction($request)
    {        
        $postData = $request->getParsedBody();
        if($postData){
            $accesoriesValidator = v::key('name', v::stringType()->notEmpty());
            try{
                $accesoriesValidator->assert($postData);
            } catch (Exception $ex) {
                $this->responseMessage = $ex->getMessage();
            } 
            $accesory = new Accesories();
            if(isset($postData['id']) && $postData['id']){
                $accesory = Accesories::find($postData['id']);
            }                              
            $accesory->name = $postData['name'];                
            $keystring = $this->normalizeKeyString($postData['name']);
            $accesory->keystring = $keystring;
            $this->responseMessage = $this->saveAccesory($accesory);
        }                            
        $selected_accesory = $this->setAccesory($request);
        return $this->renderHTML('/vehicles/accesories/accesoriesForm.html.twig', [
            'selected_accesory' => $selected_accesory,            
            'responseMessage' => $this->responseMessage
        ]);
    }
    public function saveAccesory($accesory)
    {
        try
        {
            if(Accesories::find($accesory->id))
            {
                $accesory->update();
                $message = 'Updated';
            }
            else
            {
                $accesory->save();
                $message = 'Saved';
            }
        } catch(QueryException $ex) {
            $message = $this->errorService->getError($ex);                    
        }
        return $message;
    }
    public function normalizeKeyString ($string)
    {
        $words = str_word_count($string);
        if($words > 1)
        {
            $keystring = str_replace(" ", "-", $string);
        }
        else
        {
            $keystring = $string;
        }
        $keystring = "acc-".strtolower($keystring);
        return $keystring;
    }
    public function setAccesory($request)
    {   
        $selected_accesory = null;
        if($request->getMethod() === 'GET')
        {            
            $params = $request->getQueryParams();
            if(isset($params['id']) && $params['id'])
            {            
                $selected_accesory = DB::table('accesories')                    
                        ->select('accesories.id', 'accesories.keystring', 'accesories.name')
                        ->where('accesories.id', '=', $params['id'])                   
                        ->first();
            }
        }
        return $selected_accesory;        
    }
    public function deleteAction(ServerRequest $request)
    {         
        $this->accesoriesService->deleteAccesories($request->getQueryParams('id'));               
        return new RedirectResponse('/Intranet/vehicles/accesories/list');
    }  
}
