<?php

namespace App\Controllers\Buys;

use App\Controllers\BaseController;
use Respect\Validation\Validator as v;
use App\Models\Garage;
use App\Services\Buys\GarageService;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Response\RedirectResponse;

class GaragesController extends BaseController
{    
    
    protected $garageService;

    public function __construct(GarageService $garageService)
    {
        parent::__construct();
        $this->garageService = $garageService;
    }   
    
    public function getIndexAction()
    {
        $garages = Garage::All();
        return $this->renderHTML('/buys/garagesList.twig', [
            'garages' => $garages
        ]);
    }   
    
    public function getGarageDataAction($request)
    {                
        $responseMessage = null;
        if($request->getMethod() == 'POST')
        {
            $postData = $request->getParsedBody();
            
            $garageValidator = v::key('name', v::stringType()->notEmpty()) 
            ->key('fiscal_id', v::notEmpty())
            ->key('phone', v::notEmpty())
            ->key('email', v::stringType()->notEmpty());            
            try{
                $garageValidator->assert($postData); // true 
                $garage = new Garage();
                $garage->name = $postData['name'];
                $garage->fiscal_id = $postData['fiscal_id'];                
                $garage->address = $postData['address'];
                $garage->city = $postData['city'];
                $garage->postal_code = $postData['postal_code'];
                $garage->state = $postData['state'];
                $garage->country = $postData['country'];
                $garage->phone = $postData['phone'];
                $garage->email = $postData['email'];                
                $garage->save();     
                $responseMessage = 'Saved';     
            }catch(\Exception $e){                
                $responseMessage = $e->getMessage();
            }              
        }
        $garageSelected = null;
        if($_GET)
        {
            $garageSelected = Garage::find($_GET['id']);
        }
        return $this->renderHTML('/buys/garagesForm.twig', [
            'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
            'responseMessage' => $responseMessage,
            'garage' => $garageSelected
        ]);
    }

    public function deleteAction(ServerRequest $request)
    {
         
        $this->garageService->deleteGarage($request->getQueryParams('id'));               
        return new RedirectResponse('/intranet/buys/garages/list');
    }

   

}