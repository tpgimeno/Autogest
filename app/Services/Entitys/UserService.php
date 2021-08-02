<?php

namespace App\Services\Entitys;

use App\Models\User;


class UserService
{
    public function deleteUser($id)
    {
        
        $user = User::find($id)->first();       
        $user->delete();        

    }
}