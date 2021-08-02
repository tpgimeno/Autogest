<?php

namespace App\Services;

use App\Models\Location;

/**
 * Description of ModelService
 *
 * @author tonyl
 */
class LocationService 
{
    public function deleteLocation($id)
    {              
        $loc = Location::find($id)->first();        
        $loc->delete();
    }
}