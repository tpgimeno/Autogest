<?php

namespace App\Services\Entitys;

use App\Models\Taxes;
use App\Services\BaseService;
use Exception;

/**
 * Description of ModelService
 *
 * @author tonyl
 */
class TaxesService extends BaseService
{   
    public function deleteTaxes($id) {              
        $tax = Taxes::find(intval($id))->first();      
        $tax->delete();
    }
}