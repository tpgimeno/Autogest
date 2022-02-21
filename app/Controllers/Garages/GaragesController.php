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
    public function __construct(GarageService $garageService) {
        parent::__construct();
        $this->garageService = $garageService;
    }     
    public function getIndexAction() {
        $garages = $this->garageService->getAllRegisters(new Garage());
        return $this->renderHTML('/buys/garages/garagesList.html.twig', [
            'garages' => $garages
        ]);
    }      
    public function searchGarageAction($request) {
        $postData = $request->getParsedBody();
        $searchString = $postData['searchFilter'];
        $garages = $this->garageService->searchGarage($searchString);
        return $this->renderHTML('/buys/garages/garagesList.html.twig', [
            'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
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
            'garage' => $garageSelected
        ]);
    }
    public function deleteAction(ServerRequest $request) {         
        $this->garageService->deleteRegister(new Garage(), $request->getQueryParams('id'));          
        return new RedirectResponse('/Intranet/buys/garages/list');
    }
}