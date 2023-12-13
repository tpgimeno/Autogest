<?php

namespace App\Services\Buys;

use App\Services\BaseService;


class GarageService extends BaseService
{
   public function searchGarage($searchString){       
        $garages = Garage::where('name', 'like', "%".$searchString."%")
                ->orWhere('fiscal_id', 'like', "%".$searchString."%")
                ->orWhere('fiscal_name', 'like', "%".$searchString."%")
                 ->orWhere('phone', 'like', "%".$searchString."%")
                 ->orWhere('email', 'like', "%".$searchString."%")
                ->orWhere('city', 'like', "%".$searchString."%")
                ->orWhere('state', 'like', "%".$searchString."%")
                ->WhereNull('deleted_at')
                ->get();
        return $garages;
   }
}