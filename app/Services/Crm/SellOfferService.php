<?php

namespace App\Services\Crm;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class SellOfferService
{
    public function deleteOffer($id)
    {
        $offer = \App\Models\SellOffer::find($id)->first();
        $offer->delete();
    }
}