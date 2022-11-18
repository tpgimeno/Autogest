<?php

namespace App\Controllers;

use App\Models\User;
use Laminas\Diactoros\ServerRequest;


class AdminController extends BaseController { 
    public function getDashBoardAction(ServerRequest $request) {    
        
        $params = $request->getQueryParams();
        $selected_tab = 'home';
        if($params && isset($params['selected_tab'])) {
           $selected_tab = $params['selected_tab'];
        }
        $user = User::find($_SESSION['userId']);         
        return $this->renderHTML('menu.html.twig', [
            'userEmail' => $user->getAttribute('email'),
            'selected_tab' => $selected_tab
        ]);
    }
}