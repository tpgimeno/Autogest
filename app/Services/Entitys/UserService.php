<?php

namespace App\Services\Entitys;

use App\Models\User;
use Exception;


class UserService
{
    public function findUser($array){
        $user = User::Where('email', 'like', "%".$array['email']."%")->get();
        $exist = false;
        if($user){
            $exist = true;
        }
        return $exist;
    }
    public function setUser($array){
        if($this->findUser($array)){
            $user = User::Where('email', 'like', "%".$array['email']."%")->get();            
        }else{
            $user = new User();
        }
        return $user;
    }
    public function getAllUsers(){
         $users = User::All();
         return $users;
    }
    public function saveUser($array){
        $responseMessage = null;
        try{
            $user = $this->setUser($array);    
            $user->email = $array['email'];
            $user->password = password_hash($array['password'], PASSWORD_DEFAULT);
            if($this->findUser($array)){
                $user->save();     
                $responseMessage = 'Saved';
            }else{
                $user->update();
                $responseMessage = 'Update';
            }            
        }catch(Exception $e)
        {                
            $responseMessage = $this->errorService->getError($e);
        }     
        return $responseMessage;
    }    
    public function deleteUser($id){        
        $user = User::find($id);       
        $user->delete();
    }
}