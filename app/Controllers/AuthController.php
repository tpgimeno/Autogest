<?php

namespace App\Controllers;

use App\Models\User;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Diactoros\ServerRequest;

class AuthController extends BaseController {

    public function postLogin(ServerRequest $request) {
        $responseMessage = null;
        $postData = $request->getParsedBody();
//        var_dump($request);die();
        if ($request->getMethod() == 'POST') {
            $user = User::where('email', $postData['email'])->first();
            if ($this->idUserRegistered($postData)) {
               return $this->isPassVerified($user, $postData);
            } else {
                $responseMessage = 'El usuario o el Password no es correcto';
                setcookie("AutoGest", "", 0, "/", "", false, false);
                return $this->renderHTML('login.html.twig', [
                            'responseMessage' => $responseMessage
                ]);
            }
        }
    }

    public function idUserRegistered($postData) {
        $user = User::where('email', $postData['email'])->first();
        if ($user) {
            return true;
        } else {
            return false;
        }
    }

    public function isPassVerified($user, $postData) {
        if (\password_verify($postData['password'], $user->password)) {
            setcookie("AutoGest", $user->email, time()+5184000);            
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
