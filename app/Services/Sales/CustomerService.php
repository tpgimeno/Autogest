<?php

namespace App\Services\Sales;

use App\Services\BaseService;
use Illuminate\Database\Capsule\Manager as DB;

class CustomerService extends BaseService
{
    public function searchCustomer($searchString){
        $customer = DB::table('customers')
                    ->join('customertypes', 'customers.customerType', '=', 'customertypes.id')
                    ->select('customers.id', 
                            'customers.name', 
                            'customers.fiscalId', 
                            'customers.address', 
                            'customers.city', 
                            'customers.postalCode',
                            'customers.state',
                            'customers.country',
                            'customers.phone',
                            'customers.email',
                            'customers.birthDate',
                            'customertypes.name as customerType') 
                    ->where("id", "like", "%".$searchString."%")
                    ->orWhere("name", "like", "%".$searchString."%")
                    ->orWhere("fiscal_id", "like", "%".$searchString."%")
                    ->orWhere("phone", "like", "%".$searchString."%")
                    ->orWhere("email", "like", "%".$searchString."%")
                    ->whereNull('customers.deleted_at')                    
                    ->get();   
        if(!$customer){
            $customer = $this->getCustomers();
        }
        return $customer;
    }
    public function getCustomers(){
        $customers = DB::table('customers')
                    ->join('customertypes', 'customers.customerType', '=', 'customertypes.id')
                    ->select('customers.id', 
                            'customers.name', 
                            'customers.fiscalId', 
                            'customers.address', 
                            'customers.city', 
                            'customers.postalCode',
                            'customers.state',
                            'customers.country',
                            'customers.phone',
                            'customers.email',
                            'customers.birthDate',
                            'customertypes.name as customerType')                   
                    ->whereNull('customers.deleted_at')
                    ->get();
        return $customers;
    }
    public function setCustomer($array){
        if(isset($array['id'])){
            $customer = DB::table('customers')
                    ->join('customertypes', 'customers.customerType', '=', 'customertypes.id')
                    ->select('customers.id', 
                            'customers.name', 
                            'customers.fiscalId', 
                            'customers.address', 
                            'customers.city', 
                            'customers.postalCode',
                            'customers.state',
                            'customers.country',
                            'customers.phone',
                            'customers.email',
                            'customers.birthDate',
                            'customertypes.name as customerType')
                    ->where('customers.id', '=', intval($array['id']))
                    ->whereNull('customers.deleted_at')
                    ->get()->first();
        }else{
            $customer = new Customer();
        }
        return $customer;
    }
    public function getCustomerTypes(){
        $customerTypes = DB::table('customertypes')
                ->select('customertypes.id', 'customertypes.name as iter')
                ->whereNull('customertypes.deleted_at')
                ->get();
        return $customerTypes;
    }
    public function getCustomerTypesByName($name){
        $customerType = DB::table('customertypes')
                ->select('customertypes.id')
                ->where('customertypes.name', 'like', "%".$name."%")
                ->whereNull('customertypes.deleted_at')
                ->get()->first();        
        return $customerType->id;
    }
}