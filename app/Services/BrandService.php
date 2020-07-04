<?php

use App\Models\Brand;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Services;

/**
 * Description of BrandService
 *
 * @author tonyl
 */
class BrandService 
{
    public function deleteBrand($id)
    {
        
        $customer = Brand::find($id)->first();
        $customer->delete();        

    }
}
