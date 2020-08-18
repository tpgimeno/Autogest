<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor. */

namespace App\Controllers\Buys;

use App\Controllers\BaseController;
use Respect\Validation\Validator as v;
use App\Models\Mader;
use App\Services\Buys\MaderService;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Response\RedirectResponse;

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
        return $this->renderHTML('/buys/madersList.html.twig', [
            'maders' => $maders
        ]);
    }    
    public function getMaderDataAction($request)
    {                
        $responseMessage = null;
        if($request->getMethod() == 'POST')
        {
            $postData = $request->getParsedBody();            
            $maderValidator = v::key('name', v::stringType()->notEmpty());            
            try{
                $maderValidator->assert($postData); // true 
                $mader = new Mader();
                $mader->name = $postData['name'];                
                $mader->address = $postData['address'];
                $mader->city = $postData['city'];
                $mader->postal_code = $postData['postal_code'];
                $mader->state = $postData['state'];
                $mader->country = $postData['country'];
                $mader->phone = $postData['phone'];
                $mader->email = $postData['email'];
                $mader->site = $postData['site'];
                $mader->save();     
                $responseMessage = 'Saved';     
            }catch(\Exception $e){                
                $responseMessage = $e->getMessage();
            }              
        }
        $maderSelected = null;
        if($_GET)
        {
            $maderSelected = Mader::find($_GET['id']);
        }
        return $this->renderHTML('/buys/madersForm.html.twig', [
            'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
            'responseMessage' => $responseMessage,
            'mader' => $maderSelected
        ]);
    }
    public function deleteAction(ServerRequest $request)
    {         
        $this->maderService->deleteMader($request->getQueryParams('id'));               
        return new RedirectResponse('/intranet/buys/maders/list');
    }    
}
