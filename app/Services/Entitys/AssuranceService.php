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
        $values = Assurances::join('customers', function(\Illuminate\Database\Query\JoinClause $join){
                    $join->on('assurances.owner_id', '=', 'customers.id')->orOn('assurances.getter_id', '=', 'customers.id');
                    
                    })                 
                ->join('vehicles', 'assurances.object_id', '=', 'vehicles.id')                
                ->get(['assurances.id', 'assurances.ref', 'assurances.effectDate', 'assurances.owner_id', 'assurances.price'])->toArray();
        return $values;
    }
}
