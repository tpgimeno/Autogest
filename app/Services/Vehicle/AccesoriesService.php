<?php



namespace App\Services\Vehicle;


use App\Models\Accesories;
/**
 * Description of AccesoriesService
 *
 * @author tonyl
 */
class AccesoriesService 
{
    public function deleteAccesories($id)
    {
        $accesories = Accesories::find($id)->first();
        $accesories->delete();
    }
}