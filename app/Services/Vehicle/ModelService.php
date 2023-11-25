<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Services\Vehicle;

use App\Models\ModelVh;
use App\Services\BaseService;

/**
 * Description of ModelService
 *
 * @author tonyl
 */
class ModelService extends BaseService {
    public function getModelItemsList(){
        $values = ModelVh::join('brands', 'models.brand_id', '=', 'brands.id')
                ->get(['models.id', 'brands.name as brand', 'models.name'])->toArray();
        return $values;
    }
}
  