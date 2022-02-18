<?php

namespace App\Services\Entitys;

use App\Models\Finance;
use App\Services\BaseService;


class FinanceService extends BaseService {
    public function deleteFinance($id) {        
        $finance = Finance::find($id)->first();
        $finance->delete();
    }
}