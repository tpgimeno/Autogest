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
    public function getAccounts(){
        $accounts = DB::table('accounts')
                ->select('accounts.id', 'accounts.accountNumber as iter')
                ->whereNull('deleted_at')
                ->get();
        return $accounts;
    }
    public function setPaymentWay($array){
        if(isset($array['id'])){
            $paymentWay = DB::table('paymentWays')
                    ->join('accounts', 'accounts.id', '=', 'paymentWays.account')
                    ->select('paymentWays.id', 'paymentWays.name', 'accounts.accountNumber as iter', 'paymentWays.discount')
                    ->where('paymentWays.id', '=', intval($array['id']))
                    ->whereNull('deleted_at')
                    ->get()->first();
        }else{
            $paymentWay = new PaymentWays();
        }
        return $paymentWay;
    }
}