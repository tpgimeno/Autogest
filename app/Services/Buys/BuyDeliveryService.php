<?php

namespace App\Services\Buys;

use App\Models\BuyDelivery;

class BuyDeliveryService
{
    public function deleteBuyDelivery($id)
    {
        $buyDelivery = BuyDelivery::find($id)->first();
        $buyDelivery->delete();                
    }
}