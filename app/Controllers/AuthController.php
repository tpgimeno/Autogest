<?php

namespace App\Controllers;

use App\Models\User;
use Laminas\Diactoros\Response\RedirectResponse;

class AuthController extends BaseController
{

    public function postLogin($request)
    {
        $responseMessage = null;
        $postData = $request->getParsedBody();
        if($request->getMethod() == 'POST')
        {               
            $user = User::where('email', $postData['email'])->first();
            
            if($user)
            {
                if(\password_verify($postData['password'], $user->password))
                {
                    $_SESSION['userId'] = $user->id;
                    return new RedirectResponse('/intranet/admin');                    
                }
                else
                {
                    $responseMessage = 'El usuario o el Password no es correcto';
                    return $this->renderHTML('login.twig', [
                        'responseMessage' => $responseMessage
                    ]);
                }                
            }else
            {
                $responseMessage = 'El usuario o el Password no es correcto';
                    return $this->renderHTML('login.twig', [
                        'responseMessage' => $responseMessage
                    ]);
            }           
        }
    }
    public function getLogout()
    {
        unset($_SESSION['userId']);
        return new RedirectResponse('/ ');  
    }
}