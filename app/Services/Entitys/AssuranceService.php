<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Services\Entitys;

use App\Models\Assurances;
use App\Services\BaseService;

/**
 * Description of AssuranceService
 *
 * @author tonyl
 */
class AssuranceService extends BaseService {
    public function list(){
        $values = Assurances::join('customers', 'assurances.owner_id', '=', 'customers.id')
                ->join('vehicles', 'assurances.object_id', '=', 'vehicles.id')                
                ->get(['assurances.id', 'assurances.ref', 'assurances.effectDate', 'customers.name as owner_id','vehicles.plate as object_id', 'assurances.price'])->toArray();
        return $values;
    }
}
