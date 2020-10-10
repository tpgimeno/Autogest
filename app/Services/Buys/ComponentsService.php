<?php

namespace App\Services\Buys;


use App\Models\Components;

/**
 * Description of ComponentsService
 *
 * @author tonyl
 */
class ComponentsService 
{
    public function deleteComponents($id)
    {
        $components = Components::find($id)->first();
        $components->delete();
    }
}