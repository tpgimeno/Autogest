<?php

namespace App\Controllers;

use App\Models\User;
use Laminas\Diactoros\ServerRequest;


class AdminController extends BaseController
{  
    public function getDashBoardAction(ServerRequest $request)
    {              
        $user = User::find($_SESSION['userId']);         
        return $this->renderHTML('dashboard.twig', [
            'userEmail' => $user->getAttribute('email')
        ]);
    }

}