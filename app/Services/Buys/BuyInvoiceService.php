<?php

namespace App\Services\Buys;

use App\Models\BuyInvoice;

class BuyInvoiceService
{
    public function deleteBuyInvoice($id)
    {
        $buyInvoice = BuyInvoice::find($id)->first();
        $buyInvoice->delete();                
    }
}