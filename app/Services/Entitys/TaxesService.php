<?php

namespace App\Services\Entitys;

use App\Models\Taxes;

/**
 * Description of ModelService
 *
 * @author tonyl
 */
class TaxesService 
{
    public function deleteTaxes($id)
    {              
        $tax = taxes::find($id)->first();        
        $tax->delete();
    }
}