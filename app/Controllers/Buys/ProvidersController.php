<?php

namespace App\Controllers\Buys;

use App\Controllers\BaseController;
use App\Models\Provider;
use App\Services\Buys\ProviderService;
use Exception;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Diactoros\ServerRequest;
use Respect\Validation\Validator as v;

class ProvidersController extends BaseController {   
    protected $providerService;
    public function __construct(ProviderService $providerService) {
        parent::__construct();
        $this->providerService = $providerService;
    }    
    public function getIndexAction() {
        $providers = $this->providerService->getAllRegisters(new Provider());
        return $this->renderHTML('/buys/providers/providersList.html.twig', [
            'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
            'providers' => $providers
        ]);
    } 
    public function searchProviderAction($request) {
        $postData = $request->getParsedBody();
        $searchString = $postData['searchFilter'];
        $providers = $this->providerService->searchProvider($searchString);        
        return $this->renderHTML('/buys/providers/providersList.html.twig', [
            'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
            'providers' => $providers
        ]);
    }
    public function getProviderDataAction($request) {                
        $responseMessage = null;
        if($request->getMethod() == 'POST') {
            $postData = $request->getParsedBody();            
            $providerValidator = v::key('name', v::stringType()->notEmpty()) 
            ->key('fiscalId', v::notEmpty())
            ->key('phone', v::notEmpty())
            ->key('email', v::stringType()->notEmpty());            
            try{
                $providerValidator->assert($postData); // true 
                $responseMessage = $this->providerService->saveRegister(new Provider(), $postData); 
            }catch(Exception $e){                
                $responseMessage = $e->getMessage();
            }              
        }
        $providerSelected = $this->providerService->setInstance(new Provider(), $request->getQueryParams('id'));
        return $this->renderHTML('/buys/providers/providersForm.html.twig', [
            'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
            'responseMessage' => $responseMessage,
            'provider' => $providerSelected
        ]);
    }
    public function deleteAction(ServerRequest $request) {         
        $this->providerService->deleteRegister(new Provider(), $request->getQueryParams('id'));         
        return new RedirectResponse('/Intranet/buys/providers/list');
    }    
}