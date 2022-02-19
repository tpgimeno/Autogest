<?php

namespace App\Services\Entitys;

use App\Models\Location;
use App\Models\Store;
use App\Services\BaseService;
use Illuminate\Database\Capsule\Manager as DB;

/**
 * Description of ModelService
 *
 * @author tonyl
 */
class LocationService extends BaseService {
    public function getLocations(){
        $locations = DB::table('locations')
                ->join('stores', 'stores.id', '=', 'locations.storeId')                
                ->select('locations.id', 'stores.name as store', 'locations.name')
                ->whereNull('locations.deleted_at')
                ->get();
        return $locations;
    }
    public function getStoresNames(){
        $stores = DB::table('stores')
                ->select('stores.name')
                ->get();
        return $stores;
    }
    public function searchLocations($searchString){
        $locations = DB::table('locations')
                ->join('stores', 'stores.id', '=', 'locations.store_id')                
                ->select('locations.id', 'stores.name as store', 'locations.name')                
                ->where('locations.name', 'like', "%".$searchString."%")
                ->orWhere('stores.name', 'like', "%".$searchString."%") 
                ->whereNull('locations.deleted_at')
                ->get();
        return $locations;
    }
    public function getStoreByName($array){
        if(isset($array['store'])){
            $name = $array['store'];        
            $store = Store::where('name', 'like', "%".$name."%")->get()->first();
        }else{
            $store = new Store();
        }
        return $store->id;
    }
    public function setLocationData($array){
        if(isset($array['id'])){
            $location = DB::table('locations')
                ->join('stores', 'stores.id', '=', 'locations.storeId')
                ->select('locations.id', 'locations.name', 'stores.name as store')
                ->where('locations.id', '=', intval($array['id']))
                ->get()->first();
        }else{
            $location = new Location();
        }       
        return $location;
    }
}