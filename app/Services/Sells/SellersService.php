<?php


namespace App\Services\Sells;

use App\Models\Sellers;
/**
 * Description of SellersService
 *
 * @author tonyl
 */
class SellersService 
{
    public function deleteSeller($id)
    {
        $seller = Sellers::find($id)->get();
        var_dump($seller);die();
        $seller->delete();
    }
}
