<?php

namespace App\Services\Entitys;

use App\Services\BaseService;


class FinanceService extends BaseService {
    public function list(){
        $finances = \App\Models\Finance::join('banks', 'finance.bank_id', '=', 'banks.id')
                ->get(['finance.id', 'banks.name as bank_id', 'finance.name','finance.email', 'finance.phone'])->toArray();
        return $finances;
    }
}