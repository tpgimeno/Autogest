<?php



namespace App\Services\Vehicle;

use App\Models\Mader;
use App\Models\Supplies;
use App\Services\BaseService;
use Illuminate\Database\Capsule\Manager as DB;
/**
 * Description of SuppliesService
 *
 * @author tonyl
 */
class SuppliesService extends BaseService {
    public function getSupplies(){
         $supplies = DB::table('supplies')
                ->join('maders', 'supplies.mader', '=', 'maders.id')
                ->select('supplies.id', 'supplies.ref', 'maders.name as mader', 'supplies.name', 'supplies.stock', 'supplies.maderCode', 'supplies.pvc', 'supplies.pvp')
                ->whereNull('supplies.deleted_at')
                ->get(); 
         return $supplies;
    }
    public function getMaderByName($array){
        if(isset($array['mader'])){
            $mader = DB::table('maders')
                    ->select('maders.id')
                    ->where('maders.name', 'like', "%".$array['mader']."%")
                    ->whereNull('maders.deleted_at')
                    ->get()
                    ->first();
        }else{
            $mader = new Mader();
        }
        return $mader->id;
    }
    public function setSupplyInstance($array){
        if(isset($array['id']) && $array['id']) {            
            $selected_supply = DB::table('supplies')
                    ->join('maders', 'supplies.mader', '=', 'maders.id')
                    ->select('supplies.id', 'supplies.ref', 'supplies.name', 'maders.name as mader', 'supplies.pvc', 'supplies.pvp')
                    ->where('supplies.id', '=', $array['id'])
                    ->whereNull('supplies.deleted_at')
                    ->first();
        }else{
            $selected_supply = new Supplies();
        }        
        return $selected_supply;
    }
}