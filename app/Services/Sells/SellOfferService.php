<?php

namespace App\Services\Crm;

use App\Models\SellOffer;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class SellOfferService
{
    public function deleteOffer($id)
    {
        var_dump($id);die();
        $offer = SellOffer::find($id)->first();
        $offer->delete();
    }
}