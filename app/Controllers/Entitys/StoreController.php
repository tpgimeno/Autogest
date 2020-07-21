<?php

namespace App\Controllers\Entitys;

use App\Controllers\BaseController;
use Respect\Validation\Validator as v;
use App\Models\Store;
use App\Services\StoreService;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Response\RedirectResponse;

class StoreController extends BaseController
{    
    
    protected $storeService;

    public function __construct(StoreService $storeService)
    {
        parent::__construct();
        $this->storeService = $storeService;
    }
    
    
    
    public function getIndexAction()
    {
        $store = Store::All();
        return $this->renderHTML('/stores/storeList.html.twig', [
            'stores' => $store
        ]);
    }   
    
    public function getStoreDataAction($request)
    {                
        $responseMessage = null;
        if($request->getMethod() == 'POST')
        {
            $postData = $request->getParsedBody();
            
            $storeValidator = v::key('name', v::stringType()->notEmpty())             
            ->key('address', v::notEmpty());           
            try{
                $storeValidator->assert($postData); // true 
                $store = new Store();
                $store->id = $postData['id'];
                if($store->id)
                {
                    $temp_store = Store::find($store->id)->first();
                }
                if(isset($temp_store))
                {
                    $store = $temp_store;
                }
                $store->name = $postData['name'];                
                $store->address = $postData['address'];
                $store->city = $postData['city'];
                $store->postal_code = $postData['postal_code'];
                $store->state = $postData['state'];
                $store->country = $postData['country'];
                $store->phone = $postData['phone'];
                $store->email = $postData['email'];   
                if(isset($temp_store))
                {
                    $store->update();
                    $responseMessage = 'Updated';
                }
                else
                {
                    $store->save();     
                    $responseMessage = 'Saved';  
                }
                   
            }catch(\Exception $e){                
                $responseMessage = $e->getMessage();
            }              
        }
        $storeSelected = null;
        if($request->getQueryParams('id'))
        {
            $storeSelected = Store::find($request->getQueryParams('id'))->first();
        }
        return $this->renderHTML('/stores/storeForm.html.twig', [
            'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
            'responseMessage' => $responseMessage,
            'store' => $storeSelected
        ]);
    }
    public function deleteAction(ServerRequest $request)
    {
         
        $this->storeService->deleteStore($request->getQueryParams('id'));               
        return new RedirectResponse('/intranet/store/list');
    }
}