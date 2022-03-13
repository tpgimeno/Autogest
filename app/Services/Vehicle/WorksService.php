<?php



namespace App\Services\Vehicle;

use App\Models\Works;
use App\Services\BaseService;
/**
 * Description of SuppliesService
 *
 * @author tonyl
 */
class WorksService extends BaseService {
   public function searchWorks($searchString){
        $works = Works::Where("id", "like", "%".$searchString."%")
                ->orWhere("reference", "like", "%".$searchString."%") 
                ->orWhere("description", "like", "%".$searchString."%")               
                ->get(); 
        if(!$works){
            $works = $this->getAllRegisters(new Works());
        }
        return $works;
    }
}