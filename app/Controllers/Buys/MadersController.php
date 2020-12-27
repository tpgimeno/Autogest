<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor. */

namespace App\Controllers\Buys;

use App\Controllers\BaseController;
use App\Models\Mader;
use App\Services\Buys\MaderService;
use Exception;
use Illuminate\Database\QueryException;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Diactoros\ServerRequest;
use Respect\Validation\Validator as v;

class MadersController extends BaseController
{        
    protected $maderService;    
    public function __construct(MaderService $maderService)
    {
        parent::__construct();
        $this->maderService = $maderService;
    }    
    public function getIndexAction()
    {
        $maders = Mader::All();
        return $this->renderHTML('/buys/maders/madersList.html.twig', [
            'maders' => $maders
        ]);
    }    
    public function getMaderDataAction($request)
    {                
        $responseMessage = null;
        if($request->getMethod() == 'POST')
        {
            $postData = $request->getParsedBody();            
            if($this->validateData($postData))
            {
                $responseMessage = $this->validateData($postData);
            }
            $mader = $this->addMaderData($postData);
            $responseMessage = $this->saveMader($mader); 
        }        
        $params = $request->getQueryParams();
        $maderSelected = $this->setMader($params);        
        return $this->renderHTML('/buys/maders/madersForm.html.twig', [
            'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
            'responseMessage' => $responseMessage,
            'mader' => $maderSelected
        ]);
    }
    public function validateData($postData)
    {
        $responseMessage = null;
        $maderValidator = v::key('name', v::stringType()->notEmpty());            
        try{
            $maderValidator->assert($postData); // true                    
        }catch(Exception $e){                
            $responseMessage = $e->getMessage();
        }
        return $responseMessage;
    }
    public function setMader($params)
    {
        $maderSelected = null;
        if(isset($params['id']) && $params['id'])
        {
            $maderSelected = Mader::find($params['id']);
        }
        return $maderSelected;
    }
    public function addMaderData($postData)
    {
        $mader = new Mader();
        if(isset($postData['id']) && $postData['id'])
        {
            $mader = Mader::find($postData['id'])->first();
        }        
        $mader->name = $postData['name'];                
        $mader->address = $postData['address'];
        $mader->city = $postData['city'];
        $mader->postalCode = $postData['postal_code'];
        $mader->state = $postData['state'];
        $mader->country = $postData['country'];
        $mader->phone = $postData['phone'];
        $mader->email = $postData['email'];
        $mader->site = $postData['site'];
        return $mader;
    }
    public function saveMader($mader)
    {
        try{
            if(Mader::find($mader->id))
            {
                $mader->update();
                $responseMessage = 'Updated';
            }
            else
            {
                $mader->save();     
                $responseMessage = 'Saved';
            }
        } catch (QueryException $ex) {
            $responseMessage = $this->errorService->getError($ex);
        }
        return $responseMessage;        
    }
    public function deleteAction(ServerRequest $request)
    {         
        $this->maderService->deleteMader($request->getQueryParams('id'));               
        return new RedirectResponse('/intranet/buys/maders/list');
    }    
}
