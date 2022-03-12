<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Services\Vehicle;

use App\Models\ModelVh;
use App\Services\BaseService;
use Illuminate\Database\Capsule\Manager as DB;

/**
 * Description of ModelService
 *
 * @author tonyl
 */
class ModelService extends BaseService {
    public function getModels(){
        $models = DB::table('models')
               ->join('brands', 'brands.id', '=', 'models.brandId')                
               ->select('models.id', 'brands.name as brand', 'models.name')
               ->whereNull('models.deleted_at')
               ->get();
        return $models;
    }
    public function searchModels($searchString){
        $models = DB::table('models')
               ->join('brands', 'brands.id', '=', 'models.brandId')                
               ->select('models.id', 'brands.name as brand', 'models.name')
               ->where("models.name", "like", "%".$searchString."%")
               ->orWhere("brands.name", "like", "%".$searchString."%")
               ->whereNull('models.deleted_at')
               ->get(); 
        return $models;
    }
    public function getBrands(){
        $brands = DB::table('brands')
                ->select('brands.id', 'brands.name as iter')
                ->whereNull('brands.deleted_at')
                ->get();
        return $brands;
    }
    public function getBrandByName($name){
        $brand = DB::table('brands')
                ->select('brands.id')
                ->where('brands.name', 'like', "%".$name."%")
                ->get()->first();
        return $brand->id;
    }
    public function getModel($array){
        if(isset($array['id'])){
            $model = $models = DB::table('models')
               ->join('brands', 'brands.id', '=', 'models.brandId')                
               ->select('models.id', 'brands.name as brand', 'models.name')
               ->where('models.id', '=', intval($array['id']))
               ->whereNull('models.deleted_at')
               ->get()->first();
        }else{
            $model = new ModelVh();
        }
        return $model;
    }
}
  