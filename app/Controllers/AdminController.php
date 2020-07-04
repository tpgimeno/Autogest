<?php

namespace App\Controllers;

use App\Models\User;
use Laminas\Diactoros\ServerRequest;


class AdminController extends BaseController
{  
    public function getDashBoardAction(ServerRequest $request)
    {              
        $user = User::where('id', $_SESSION['userId'])->first();         
        return $this->renderHTML('dashboard.twig', [
            'userEmail' => $user->getAttribute('email')
        ]);
    }

}