<?php

namespace App\Services;

use App\Models\Finance;


class FinanceService
{
    public function deleteFinance($id)
    {
        
        $finance = Finance::find($id)->first();
        $finance->delete();        

    }
}