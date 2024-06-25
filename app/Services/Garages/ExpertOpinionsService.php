<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace App\Services\Garages;

use App\Models\ExpertOpinions;
use App\Services\BaseService;

/**
 * Description of ExpertOpinionsService
 *
 * @author tonyl
 */
class ExpertOpinionsService extends BaseService{
    
    public function list(){
        $values = ExpertOpinions::join('vehicles', 'expertopinions.vehicle_id', '=', 'vehicles.id')                
                ->join('brands', 'vehicles.brand_id', '=', 'brands.id')
                ->join('models', 'vehicles.model_id', '=', 'models.id')
                ->get(['expertopinions.id as id', 'expertopinions.expertName as name', 'expertopinions.expertSurname as surname', 'vehicles.plate as plate', 'expertopinions.date as date'])
                ->toArray();
        return $values;
    }
    
     public function getLastOpinionId(){
        $lastOpinionId = ExpertOpinion::select('expertopinions.opinionId')
                ->get()->last();
        return $lastOpinionId;        
    }
}
