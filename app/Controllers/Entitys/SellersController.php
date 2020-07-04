<?php

namespace App\Controllers\Entitys;

use App\Controllers\BaseController;
use Respect\Validation\Validator as v;
use App\Models\Sellers;
use App\Services\SellersService;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Response\RedirectResponse;

class SellersController extends BaseController
{    
    
    protected $sellersService;

    public function __construct(SellersService $sellersService)
    {
        parent::__construct();
        $this->sellersService = $sellersService;
    }
    
    
    
    public function getIndexAction()
    {
        $sellers = Sellers::All();
        return $this->renderHTML('/sellers/sellersList.twig', [
            'sellers' => $sellers
        ]);
    }   
    
    public function getSellersDataAction($request)
    {                
        $responseMessage = null;
        if($request->getMethod() == 'POST')
        {
            $postData = $request->getParsedBody();
            
            $sellersValidator = v::key('name', v::stringType()->notEmpty()) 
            ->key('fiscal_id', v::notEmpty())
            ->key('phone', v::notEmpty())
            ->key('email', v::stringType()->notEmpty());            
            try{
                $sellersValidator->assert($postData); // true 
                $sellers = new Sellers();
                $sellers->name = $postData['name'];
                $sellers->fiscal_id = $postData['fiscal_id'];                
                $sellers->address = $postData['address'];
                $sellers->city = $postData['city'];
                $sellers->postal_code = $postData['postal_code'];
                $sellers->state = $postData['state'];
                $sellers->country = $postData['country'];
                $sellers->phone = $postData['phone'];
                $sellers->email = $postData['email'];               ;
                $sellers->save();     
                $responseMessage = 'Saved';     
            }catch(\Exception $e){                
                $responseMessage = $e->getMessage();
            }              
        }
        $sellersSelected = null;
        if($_GET)
        {
            $sellersSelected = Sellers::find($_GET['id']);
        }
        return $this->renderHTML('/sellers/sellersForm.twig', [
            'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
            'responseMessage' => $responseMessage,
            'sellers' => $sellersSelected
        ]);
    }

    public function deleteAction(ServerRequest $request)
    {
         
        $this->sellersService->deleteSellers($request->getQueryParams('id'));               
        return new RedirectResponse('/intranet/sellers/list');
    }

   

}