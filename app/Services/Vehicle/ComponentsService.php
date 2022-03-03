<?php

namespace App\Services\Vehicle;

use App\Models\Mader;
use App\Services\BaseService;
use Illuminate\Database\Capsule\Manager as DB;
/**
 * Description of ComponentsService
 *
 * @author tonyl
 */
class ComponentsService extends BaseService {    
    public function getComponents(){
        $components = DB::table('components')
                ->join('maders', 'maders.id', '=', 'components.mader')
                ->select('components.id', 'components.name', 'maders.name as mader', 'components.ref', 'components.serialNumber', 'observations', 'pvc', 'pvp')
                ->whereNull('components.deleted_at')
                ->get();       
        return $components;
    }
    public function getMaders(){
        $maders = DB::table('maders')
                ->select('maders.id', 'maders.name as iter')
                ->whereNull('maders.deleted_at')
                ->get()->toArray();
        return $maders;
    }
    public function getMaderByName($string){
        if(isset($string)){
            $mader = DB::table('maders')
                    ->select('maders.id')
                    ->where('maders.name', 'like', "%".$string."%")
                    ->first();
        }else{
            $mader = null;
        }
        return $mader->id;
    }
    public function setComponentInstance($array){
        $component = null;
        if(isset($array['id'])){
             $component = DB::table('components')
                ->join('maders', 'maders.id', '=', 'components.mader')
                ->select('components.id', 'components.name', 'maders.name as iter', 'components.ref', 'components.serialNumber', 'observations', 'pvc', 'pvp')
                ->where('components.id', '=', intval($array['id']))
                ->whereNull('components.deleted_at')
                ->get()
                ->first();
        }
        return $component;        
    }
}