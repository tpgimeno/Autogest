<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Services\Entitys;

use App\Models\Accounts;
use App\Models\Bank;
use App\Models\Company;
use App\Services\BaseService;
use Illuminate\Database\Capsule\Manager as DB;

/**
 * Description of AccountService
 *
 * @author tonyl
 */
class AccountService extends BaseService {
    public function getAllAccounts(){
        $accounts = DB::table('accounts')
                ->join('banks', 'accounts.bank', '=', 'banks.id')
                ->join('company', 'accounts.owner', '=', 'company.id')
                ->select('accounts.id', 'banks.name as bank', 'company.name as owner', 'accounts.accountNumber', 'accounts.observations')
                ->orderBy('banks.name')
                ->whereNull('accounts.deleted_at')
                ->get();
        return $accounts;
    }
    public function getBankByName($array){
        $name = $array['bank'];
        $bank = Bank::where('name', 'like', "%".$name."%")->get()->first(); 
        if(!$bank){
            $bank = new Bank();
        }
        return $bank->id;
    }
    public function getOwnerByName($array){
        $name = $array['owner'];
        $owner = Company::where('name', 'like', "%".$name."%")->get()->first();
        return $owner->id;
    }
    public function getBankNames(){
        $banks = DB::table('banks')
                ->select('banks.name as iter')
                ->whereNull('deleted_at')
                ->get();
        return $banks;
    }
    public function getOwnerNames(){
        $owners = DB::table('company')
                ->select('company.name as iter')
                ->whereNull('deleted_at')
                ->get();
        return $owners;
    }
    public function setAccountData($array){
         if(isset($array['id'])) {
            $instance = DB::table('accounts')
                ->join('banks', 'accounts.bank', '=', 'banks.id')
                ->join('company', 'accounts.owner', '=', 'company.id')
                ->select('accounts.id', 'banks.name as bank', 'company.name as owner', 'accounts.accountNumber', 'accounts.observations')
                ->orderBy('banks.name')
                ->where('accounts.id', '=', intval($array['id']))
                ->whereNull('accounts.deleted_at')
                ->get()->first();        
        }else{
            $instance = new Accounts();        
        }        
        return $instance;
    }
}
