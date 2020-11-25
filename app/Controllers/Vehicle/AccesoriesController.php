<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Controllers\Vehicle;

use App\Controllers\BaseController;
use App\Models\Accesories;
use App\Services\Buys\AccesoriesService;
use Illuminate\Database\Capsule\Manager as DB;
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
    
    public function __construct(AccesoriesService $accesoriesService) {
        parent::__construct();
        $this->accesoriesService = $accesoriesService;
    }
    public function getIndexAction()
    {
        $accesories = DB::table('accesories')               
                ->select('accesories.id', 'accesories.name')
                ->get();        
        return $this->renderHTML('/vehicles/accesoriesList.html.twig', [
            'accesories' => $accesories
        ]);
    }
    
    public function getAccesoryDataAction($request)
    {               
        $keystring = null;        
        $postData = $request->getParsedBody();           
        $accesoriesValidator = v::key('name', v::stringType()->notEmpty());
        try{
            $accesoriesValidator->assert($postData);
            $accesory = new Accesories();
            if(isset($postData['id']) && $postData['id'])
            {
                $accesory = Accesories::find($postData['id']);
            }                              
            $accesory->name = $postData['name'];                
            $words = str_word_count($postData['name']);
            if($words > 1)
            {
                $keystring = str_replace(" ", "-", $postData['name']);
            }
            else
            {
                $keystring = $postData['name'];
            }
            $keystring = "acc-".strtolower($keystring);
            $accesory->keystring = $keystring;
            $responseMessage = $this->saveAccesory($accesory);
        } catch (Exception $ex) {
            $responseMessage = $ex->getMessage();
        }              
        $this->renderAccesory($request, $responseMessage);
    }
    public function saveAccesory($accesory)
    {
        if(Accesories::find($accesory->id))
        {
            $accesory->update();
            $responseMessage = 'Updated';
        }
        else
        {
            $accesory->save();
            $responseMessage = 'Saved';
        }
        return $responseMessage;
    }
    public function renderAccesory($request, $responseMessage)
    {
        $selected_accesory = null;
        $params = $request->getQueryParams();
        if(isset($params['id']) && $params['id'])
        {            
            $selected_accesory = DB::table('accesories')                    
                    ->select('accesories.id', 'accesories.keystring', 'accesories.name')
                    ->where('accesories.id', '=', $params['id'])                   
                    ->first();
        }        
        return $this->renderHTML('/vehicles/accesoriesForm.html.twig', [
            'selected_accesory' => $selected_accesory,            
            'responseMessage' => $responseMessage
        ]);
    }
    public function deleteAction(ServerRequest $request)
    {         
        $this->accesoriesService->deleteAccesories($request->getQueryParams('id'));               
        return new RedirectResponse('/intranet/vehicles/accesories/list');
    }  
}
