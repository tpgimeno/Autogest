<?php

namespace App\Controllers;

use App\Models\User;
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
                return $this->isPassVerified($user, $postData);
            } else {
                $responseMessage = 'El usuario o el Password no es correcto';
                setcookie("AutoGest", "", 0, "/");
                return $this->renderHTML('login.html.twig', [
                            'responseMessage' => $responseMessage
                ]);
            }
        }
    }

    public function isPassVerified($user, $postData) {
        if (\password_verify($postData['password'], $user->password)) {
            setcookie("AutoGest", $user->email, time() + 5184000);
            $_SESSION['userId'] = $user->id;
            return new RedirectResponse('/Intranet/admin');
        } else {
            $responseMessage = 'El usuario o el Password no es correcto';
            setcookie("AutoGest", "", 0, "/", "", false, false);
            return $this->renderHTML('login.html.twig', [
                        'responseMessage' => $responseMessage
            ]);
        }
    }

    public function getLogout() {
        unset($_SESSION['userId']);
        return new RedirectResponse('/Intranet/');
    }

}
