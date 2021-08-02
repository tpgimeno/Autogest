<?php

namespace App\Controllers\Buys;

use App\Controllers\BaseController;
use Respect\Validation\Validator as v;
use App\Models\BuyInvoice;
use App\Services\Buys\BuyInvoiceService;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Response\RedirectResponse;

class BuyInvoicesController extends BaseController
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
        return $this->renderHTML('/buys/buy_invoicesList.html.twig', [
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
                $buyInvoice->id = $postData['id'];
                if($buyInvoice->id)
                {
                    $temp_buyInvoice = BuyInvoice::find($buyInvoice->id)->first();
                }
                if(isset($temp_buyInvoice))
                {
                    $buyInvoice = $temp_buyInvoice;
                }
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
                if(isset($temp_buyInvoice))
                {
                    $buyInvoice->update();
                    $responseMessage = 'Updated';
                }
                else
                {
                    $buyInvoice->save();     
                    $responseMessage = 'Saved';  
                }
                   
            }catch(\Exception $e)
            {                
                $responseMessage = $e->getMessage();
            }              
        }
        $buyInvoiceSelected = null;
        if($request->getQueryParams('id'))
        {
            $buyInvoiceSelected = BuyInvoice::find($request->getQueryParams('id'))->first();
        }
        return $this->renderHTML('/buys/buy_invoicesForm.html.twig', [
            'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
            'responseMessage' => $responseMessage,
            'buyInvoice' => $buyInvoiceSelected
        ]);
    }
    public function deleteAction(ServerRequest $request)
    {         
        $this->buyInvoiceService->deleteBuyInvoice($request->getQueryParams('id'));               
        return new RedirectResponse('/Intranet/buys/invoices/list');
    }

   

}