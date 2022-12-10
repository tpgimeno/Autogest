<?php


namespace App\Services;

require_once "../vendor/autoload.php";

use App\Models\User;


class CurrentUserService 
{   
    protected $user;
    public function getCurrentUserEmailAction()
    {
        $this->user = User::find($_COOKIE['userId']); 
        
        if(!$this->user)
        {
            $this->user->email = 'Not Registered';
        }
        return $this->user->email;
    }
}