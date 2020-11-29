<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Services\Sells;

/**
 * Description of CustomerTypesService
 *
 * @author tonyl
 */
class CustomerTypesService 
{
    public function deleteCustomerType($id)
    {
        $customerType = CustomerTypes::find($id)->first();
        $customerType->delete();
    }
}
