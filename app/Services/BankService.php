<?php

namespace App\Services;

use App\Models\Bank;


class BankService
{
    public function deleteBank($id)
    {        
        $bank = Bank::find($id)->first();
        $bank->delete();
    }
}