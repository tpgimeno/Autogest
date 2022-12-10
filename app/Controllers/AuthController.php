<?php

namespace App\Controllers;

use App\Services\AuthService;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Diactoros\ServerRequest;

class AuthController extends BaseController {

    protected $authService;

    public function __construct(AuthService $authService) {
        parent::__construct();
        $this->authService = $authService;
    }
    public function postLogin(ServerRequest $request) {
        $responseMessage = null;
        $postData = $request->getParsedBody();        
        if ($request->getMethod() == 'POST') {             
            $user = $this->authService->idUserRegistered($postData);            
            if ($user) {                
                setcookie("AutoGest", $user->email, time() + 5184000);
                $__COOKIE['userId'] = $user->id;              
                return new RedirectResponse('/Intranet/admin');                
            } else {
                $responseMessage = 'El usuario o el Password no es correcto';                
                setcookie("AutoGest", "", 0, "/");
                return $this->renderHTML('login.html.twig', [
                            'responseMessage' => $responseMessage
                ]);
            }
        }
    }
    public function getLogout() {
        unset($_SESSION['userId']);
        return new RedirectResponse('/Intranet/');
    }

}
