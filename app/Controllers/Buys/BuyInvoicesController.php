<?php

namespace App\Controllers\Buys;

use App\Controllers\BaseController;
use Respect\Validation\Validator as v;
use App\Models\BuyInvoice;
use App\Services\Buys\BuyInvoiceService;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Response\RedirectResponse;

class BuyInvoiceController extends BaseController
{    
    
    protected $buyInvoiceService;

    public function __construct(BuyInvoiceService $buyInvoiceService)
    {
        parent::__construct();
        $this->buyInvoiceService = $buyInvoiceService;
    }
    
    
    
    public function getIndexAction()
    {
        $buyInvoices = BuyInvoice::All();
        return $this->renderHTML('/buys/buy_invoicesList.twig', [
            'buyInvoices' => $buyInvoices
        ]);
    }   
    
    public function getBuyInvoiceDataAction($request)
    {                
        $responseMessage = null;
        if($request->getMethod() == 'POST')
        {
            $postData = $request->getParsedBody();
            
            $buyInvoiceValidator = v::key('name', v::stringType()->notEmpty()) 
            ->key('fiscal_id', v::notEmpty())
            ->key('phone', v::notEmpty())
            ->key('email', v::stringType()->notEmpty());            
            try{
                $buyInvoiceValidator->assert($postData); // true 
                $buyInvoice = new BuyInvoice();
                $buyInvoice->name = $postData['name'];
                $buyInvoice->fiscal_id = $postData['fiscal_id'];
                $buyInvoice->fiscal_name = $postData['fiscal_name'];
                $buyInvoice->address = $postData['address'];
                $buyInvoice->city = $postData['city'];
                $buyInvoice->postal_code = $postData['postal_code'];
                $buyInvoice->state = $postData['state'];
                $buyInvoice->country = $postData['country'];
                $buyInvoice->phone = $postData['phone'];
                $buyInvoice->email = $postData['email'];
                $buyInvoice->site = $postData['site'];
                $buyInvoice->save();     
                $responseMessage = 'Saved';     
            }catch(\Exception $e){                
                $responseMessage = $e->getMessage();
            }              
        }
        $buyInvoiceSelected = null;
        if($_GET)
        {
            $buyInvoiceSelected = BuyInvoice::find($_GET['id']);
        }
        return $this->renderHTML('/buys/buy_invoicesForm.twig', [
            'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
            'responseMessage' => $responseMessage,
            'buyInvoice' => $buyInvoiceSelected
        ]);
    }

    public function deleteAction(ServerRequest $request)
    {
         
        $this->buyInvoiceService->deleteBuyInvoice($request->getQueryParams('id'));               
        return new RedirectResponse('/intranet/buyInvoices/list');
    }

   

}