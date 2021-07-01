<?php

namespace App\Controllers\Buys;

use App\Controllers\BaseController;
use App\Models\Provider;
use App\Services\Buys\ProviderService;
use Exception;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Diactoros\ServerRequest;
use Respect\Validation\Validator as v;

class ProvidersController extends BaseController
{    
    
    protected $providerService;

    public function __construct(ProviderService $providerService)
    {
        parent::__construct();
        $this->providerService = $providerService;
    }    
    public function getIndexAction()
    {
        $providers = Provider::All();
        return $this->renderHTML('/buys/providers/providersList.html.twig', [
            'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
            'providers' => $providers
        ]);
    } 
    public function searchProviderAction($request)
    {
        $postData = $request->getParsedBody();
        $searchString = $postData['searchFilter'];
        $providers = Provider::where('name', 'like', "%".$searchString."%")
                ->orWhere('fiscal_id', 'like', "%".$searchString."%")
                ->orWhere('fiscal_name', 'like', "%".$searchString."%")
                ->orWhere('city', 'like', "%".$searchString."%")
                ->orWhere('state', 'like', "%".$searchString."%")
                ->WhereNull('deleted_at')
                ->get();
        
        return $this->renderHTML('/buys/providers/providersList.html.twig', [
            'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
            'providers' => $providers
        ]);
    }
    public function getProviderDataAction($request)
    {                
        $responseMessage = null;
        if($request->getMethod() == 'POST')
        {
            $postData = $request->getParsedBody();            
            $providerValidator = v::key('name', v::stringType()->notEmpty()) 
            ->key('fiscal_id', v::notEmpty())
            ->key('phone', v::notEmpty())
            ->key('email', v::stringType()->notEmpty());            
            try{
                $providerValidator->assert($postData); // true 
                $provider = new Provider();
                $provider->id = $postData['id'];
                if($provider->id)
                {
                    $provider_temp = Provider::find($provider->id)->first();
                    if($provider_temp)
                    {
                        $provider = $provider_temp;
                    }
                }
                $provider->name = $postData['name'];
                $provider->fiscal_id = $postData['fiscal_id'];
                $provider->fiscal_name = $postData['fiscal_name'];
                $provider->address = $postData['address'];
                $provider->city = $postData['city'];
                $provider->postal_code = $postData['postal_code'];
                $provider->state = $postData['state'];
                $provider->country = $postData['country'];
                $provider->phone = $postData['phone'];
                $provider->email = $postData['email'];
                $provider->site = $postData['site'];
                if($provider_temp)
                {
                    $provider->update();
                    $responseMessage = 'Updated';
                }
                else
                {
                    $provider->save();     
                    $responseMessage = 'Saved';
                }                     
            }catch(Exception $e){                
                $responseMessage = $e->getMessage();
            }              
        }
        $providerSelected = null;
        if($_GET)
        {
            $providerSelected = Provider::find($_GET['id']);
        }
        return $this->renderHTML('/buys/providers/providersForm.html.twig', [
            'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
            'responseMessage' => $responseMessage,
            'provider' => $providerSelected
        ]);
    }
    public function deleteAction(ServerRequest $request)
    {         
        $this->providerService->deleteProvider($request->getQueryParams('id'));               
        return new RedirectResponse('/intranet/buys/providers/list');
    }    
}