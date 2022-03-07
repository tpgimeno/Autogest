<?php



namespace App\Services\Vehicle;

use App\Services\BaseService;
use Illuminate\Database\Capsule\Manager as DB;
/**
 * Description of AccesoriesService
 *
 * @author tonyl
 */
class AccesoriesService extends BaseService
{
   public function getAccesories(){
       $accesories = DB::table('accesories')               
                ->select('accesories.id', 'accesories.name', 'accesories.keyString')
                ->get();  
       return $accesories;
   }
   public function normalizeKeyString($string) {
        $words = str_word_count($string);
        if($words > 1) {
            $keystring = str_replace(" ", "-", $string);
        }else{
            $keystring = $string;
        }
        $keystring = "acc-".strtolower($keystring);
        return $keystring;
    }
}