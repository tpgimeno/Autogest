<?php

namespace App\Services\Buys;


use App\Models\Supplies;

/**
 * Description of SuppliesService
 *
 * @author tonyl
 */
class SuppliesService 
{
    public function deleteSupplies($id)
    {
        $supplies = Supplies::find($id)->first();
        $supplies->delete();
    }
}