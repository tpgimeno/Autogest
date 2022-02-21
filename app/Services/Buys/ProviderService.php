<?php

namespace App\Services\Buys;

use App\Models\Provider;
use App\Services\BaseService;


class ProviderService extends BaseService {
    public function searchProvider($searchString){        
        $providers = Provider::where('name', 'like', "%".$searchString."%")
                ->orWhere('fiscal_id', 'like', "%".$searchString."%")
                ->orWhere('fiscal_name', 'like', "%".$searchString."%")
                ->orWhere('city', 'like', "%".$searchString."%")
                ->orWhere('state', 'like', "%".$searchString."%")
                ->WhereNull('deleted_at')
                ->get();
        return $providers;
    }
}