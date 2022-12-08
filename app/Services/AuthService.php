<?php

namespace App\Services;

use App\Models\User;

class AuthService extends BaseService{
    public function idUserRegistered($postData) {
        $user = User::where('email', $postData['email'])->first();
        if ($user) {
            if($this->isPassVerified($user, $postData)){
                return $user;
            }else{
                return false;
            }           
        } else {
            return false;
        }
    }
    public function isPassVerified($user, $postData) {
        if (\password_verify($postData['password'], $user->password)) {            
            return $user;            
        } else {            
            return null;            
        }
    }
}

