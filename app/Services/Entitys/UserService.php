<?php

namespace App\Services\Entitys;

use App\Models\User;


class UserService
{
    public function findUser($array){
        $user = User::Where('email', 'like', "%".$array['email']."%")->get();
        if($user){
            return true;
        }else{
            return false;
        }        
    }
    public function saveUser($array){
        $responseMessage = null;
        if($this->findUser($array)){
            $user = User::Where('email', 'like', "%".$array['email']."%")->get();
        }else{
            $user = new User();
        }
        $user->email = $array['email'];
        $user->password = password_hash($array['password'], PASSWORD_DEFAULT);
        $user->save();     
        $responseMessage = 'Saved'; 
        return $responseMessage;
    }    
    public function deleteUser($id){        
        $user = User::find($id);       
        $user->delete();
    }
}