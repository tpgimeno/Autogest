<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Services;

use App\Models\ModelVh;

/**
 * Description of ModelService
 *
 * @author tonyl
 */
class ModelService 
{
    public function deleteModel($id)
    {              
        $mod = ModelVh::find($id)->first();        
        $mod->delete();
    }
}
