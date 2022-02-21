<?php

namespace App\Services\Entitys;

use App\Models\Store;


class StoreService
{
    public function deleteStore($id)
    {
        
        $store = Store::find($id)->first();
        $store->delete();        

    }
}