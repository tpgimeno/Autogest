<?php



namespace App\Services\Buys;


use App\Models\Works;
/**
 * Description of SuppliesService
 *
 * @author tonyl
 */
class WorksService 
{
    public function deleteWorks($id)
    {
        $works = Works::find($id)->first();
        $works->delete();
    }
}