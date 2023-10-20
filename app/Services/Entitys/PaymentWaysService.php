<?php

namespace App\Services\Entitys;

use App\Services\BaseService;

/**
 * Description of ModelService
 *
 * @author tonyl
 */
class PaymentWaysService extends BaseService {
    public function getPaymentWaysItemsList(){
        $values = \App\Models\PaymentWays::join('accounts', 'paymentways.account_id', '=', 'accounts.id')
                ->join('banks', 'accounts.bank_id', '=', 'banks.id')
                ->get([ 'paymentways.id', 'paymentways.name', 'banks.name as bank', 'accounts.accountNumber' ])->toArray();
        return $values;
    }
    
}