<?php

namespace App\Services\Entitys;

use App\Models\Location;
use App\Services\BaseService;

/**
 * Description of ModelService
 *
 * @author tonyl
 */
class LocationService extends BaseService {
    
    public function getLocationItemsList(){
        $locations = Location::join('stores', 'locations.store_id', '=', 'stores.id')
                ->get(['locations.id', 'stores.name as store', 'locations.name'])->toArray();        
        return $locations;
    }
}