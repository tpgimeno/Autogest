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
    public function searchStore($request)
    {
        $responseMessage = null;
        $postData = $request->getParsedBody();
        $searchString = $postData['searchFilter'];
        $store = Store::where('name', 'like', "%".$searchString."%")
                ->orWhere('address', 'like', "%".$searchString."%")
                ->orWhere('city', 'like', "%".$searchString."%")
                ->orWhere('phone', 'like', "%".$searchString."%")
                ->orWhere('email', 'like', "%".$searchString."%")
                ->WhereNull('deleted_at')
                ->get();
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
            $storeValidator = v::key('name', v::stringType()->notEmpty());           
            try
            {
                $storeValidator->assert($postData); // true 
                $store = new Store();
                $store->id = $postData['id'];                
                $temp_store = Store::find($store->id)->first();
                if(isset($temp_store))
                {
                    updateData($postData);
                    $responseMessage = 'Updated';
                }
                else
                {
                    saveData($postData);     
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
    public function saveData($data)
    {
        $store = new Store();
        $store->id = $data['id'];
        $store->name = $data['name'];                
        $store->address = $data['address'];
        $store->city = $data['city'];
        $store->postal_code = $data['postal_code'];
        $store->state = $data['state'];
        $store->country = $data['country'];
        $store->phone = $data['phone'];
        $store->email = $data['email'];
        $store->save();         
    }
    public function updateData($data)
    {
        $store = new Store();
        $store->id = $data['id'];
        $store->name = $data['name'];                
        $store->address = $data['address'];
        $store->city = $data['city'];
        $store->postal_code = $data['postal_code'];
        $store->state = $data['state'];
        $store->country = $data['country'];
        $store->phone = $data['phone'];
        $store->email = $data['email'];
        $store->update(); 
    }
    public function deleteAction(ServerRequest $request)
    {
         
        $this->storeService->deleteStore($request->getQueryParams('id'));               
        return new RedirectResponse('/intranet/store/list');
    }
}