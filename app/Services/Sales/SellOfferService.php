<?php

namespace App\Services\Sales;

use App\Models\SellOffer;
use Illuminate\Database\Capsule\Manager as DB;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class SellOfferService {
    public function getSellOffers(){
        $offers = DB::table('selloffers')
                ->join('customers', 'selloffers.customerId', '=', 'customers.id')
                ->join('vehicles', 'selloffers.vehicleId', '=', 'vehicles.id')
                ->join('brands', 'vehicles.brand', '=', 'brands.id')
                ->join('models', 'vehicles.model', '=', 'models.id')
                ->select('selloffers.id', 'selloffers.offerNumber', 'selloffers.offerDate', 'customers.name as name', 'brands.name as brand', 'models.name as model')
                ->whereNull('selloffers.deleted_at')
                ->get(); 
        return $offers;
    }
    public function searchSellOffer($searchString){
        $offers = DB::table('selloffers')
                ->join('customers', 'selloffers.customerId', '=', 'customers.id')
                ->join('vehicles', 'selloffers.vehicleId', '=', 'vehicles.id')
                ->join('brands', 'vehicles.brand', '=', 'brands.id')
                ->join('models', 'vehicles.model', '=', 'models.id')          
                ->select('selloffers.offerNumber', 'customers.name as customerName', 'brands.name as brand',
                        'models.name as model')
                ->where('selloffers.offerDate', 'like', '%'.$searchString.'%')
                ->orWhere('selloffers.offerNumber', 'like', '%'.$searchString.'%')
                ->orWhere('customers.name', 'like', '%'.$searchString.'%')
                ->orWhere('brands.name', 'like', '%'.$searchString.'%')
                ->orWhere('models.name', 'like', '%'.$searchString.'%')
                ->whereNull('deleted_at')
                ->get(); 
        if(!$offers){
            $offers = $this->getSellOffers();
        }
        return $offers;
    }
    public function saveOffer($array){
        
    }
    public function deleteOffer($id){
        var_dump($id);die();
        $offer = SellOffer::find($id)->first();
        $offer->delete();
    }
}