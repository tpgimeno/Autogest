<?php

namespace App\Services\Entitys;

use App\Models\Store;
use App\Services\BaseService;


class StoreService extends BaseService {
    public function searchStore($searchString) {        
        $store = Store::where('name', 'like', "%".$searchString."%")
                ->orWhere('address', 'like', "%".$searchString."%")
                ->orWhere('city', 'like', "%".$searchString."%")
                ->orWhere('phone', 'like', "%".$searchString."%")
                ->orWhere('email', 'like', "%".$searchString."%")
                ->WhereNull('deleted_at')
                ->get();
        return $store;
    }
}