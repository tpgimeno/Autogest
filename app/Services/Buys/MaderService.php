<?php

namespace App\Services\Buys;

use App\Models\Mader;

class MaderService
{
    public function deleteMader($id)
    {
        
        $mader = Mader::find($id)->first();
        $mader->delete();        

    }
}