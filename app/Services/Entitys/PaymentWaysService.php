<?php

namespace App\Services\Entitys;

use App\Models\Accounts;
use App\Models\PaymentWays;
use App\Services\BaseService;
use Illuminate\Database\Capsule\Manager as DB;

/**
 * Description of ModelService
 *
 * @author tonyl
 */
class PaymentWaysService extends BaseService {
    public function searchPaymentWay($searchString){
        $paymentWays = DB::table('paymentWays')                            
                ->where('paymentWays.name', 'like', "%".$searchString."%")
                ->orWhere('paymentWays.percentaje', 'like', "%".$searchString."%") 
                ->whereNull('paymentWays.deleted_at')
                ->get();
        if(!$paymentWays){
            $paymentWays = $this->getAllRegisters(new PaymentWays());
        }
        return $paymentWays;
    }
    public function getAccountByNumber($array){
        if(isset($array['account'])){
            $account = DB::table('accounts')
                    ->select('accounts.id')
                    ->where('accounts.accountNumber', 'like', "%".$array['account']."%")
                    ->get()
                    ->first();
        }else{
            $account = new Accounts();
        }
        return $account->id;
    }
}