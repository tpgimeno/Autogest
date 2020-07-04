<?php

namespace App\Services\Buys;

use App\Models\Provider;


class ProviderService
{
    public function deleteProvider($id)
    {
        
        $provider = Provider::find($id)->first();
        $provider->delete();        

    }
}