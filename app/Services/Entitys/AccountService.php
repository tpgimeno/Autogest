<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Services\Entitys;

use App\Models\Accounts;
use App\Services\BaseService;

/**
 * Description of AccountService
 *
 * @author tonyl
 */
class AccountService extends BaseService {
    public function getAccountItemsList(){
        $values = Accounts::join('banks', 'accounts.bank_id', '=', 'banks.id')
                ->get(['accounts.id', 'accounts.accountNumber', 'banks.name as bank', 'accounts.owner'])->toArray();
        return $values;
    }
}
