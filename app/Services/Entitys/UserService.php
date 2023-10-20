<?php

namespace App\Services\Entitys;

use App\Models\User;
use App\Services\BaseService;


class UserService extends BaseService
{
    public function getUserItemsList(){
        $values = User::join('userlevels', 'users.userlevels_id', '=', 'userlevels.id')
                ->get(['users.id', 'users.email', 'users.name', 'userlevels.name as access', 'users.phone'])->toArray();
        return $values;
    }
}