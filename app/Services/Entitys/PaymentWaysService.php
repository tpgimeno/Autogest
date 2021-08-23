<?php

namespace App\Services\Entitys;

use App\Models\PaymentWays;

/**
 * Description of ModelService
 *
 * @author tonyl
 */
class PaymentWaysService 
{
    public function deletepaymentWay($id)
    {              
        $loc = paymentWays::find($id)->first();        
        $loc->delete();
    }
}