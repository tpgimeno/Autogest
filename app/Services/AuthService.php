<?php

/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

namespace App\Services;

use App\Models\User;

class AuthService{
    public function idUserRegistered($postData) {
        $user = User::where('email', $postData['email'])->first();
        if ($user) {
            return $user;
        } else {
            return false;
        }
    }
}

