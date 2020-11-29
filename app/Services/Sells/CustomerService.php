<?php

namespace App\Services;

use App\Models\Customer;


class CustomerService
{
    public function deleteCustomer($id)
    {
        
        $customer = Customer::find($id)->first();
        $customer->delete();        

    }
}