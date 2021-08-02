<?php

namespace App\Controllers\Entitys;

use App\Models\User;
use App\Controllers\BaseController;
use Respect\Validation\Validator as v;
use App\Services\Entitys\UserService;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Response\RedirectResponse;

class UsersController extends BaseController 
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        parent::__construct();
        $this->userService = $userService;
    }



    public function getAddUserAction($request)
    {
        $responseMessage = null;
        if($request->getMethod() == 'POST')
        {
            $postData = $request->getParsedBody();
            $userValidator = v::key('email', v::stringType()->notEmpty()) 
            ->key('password', v::stringType()->notEmpty());            
            try
            {
                // var_dump($request->getParsedBody());die;   
                $userValidator->assert($postData); // true 
                $user = new User();
                $user->email = $postData['email'];
                $user->password = password_hash($postData['password'], PASSWORD_DEFAULT);
                $user->save();     
                $responseMessage = 'Saved';  
              
            }catch(\Exception $e)
            {                
                $responseMessage = $this->errorService->getError($e);
            }              
        }  
        $selected_user = null;
        if($request->getQueryParams('id'))
        {
            $selected_user = User::find($request->getQueryParams('id'))->first();
        }
        return $this->renderHTML('/Entitys/users/userForm.twig', [
            'responseMessage' => $responseMessage,
            'user' => $selected_user
        ]);         
    }

    public function getIndexUsers()
    {
        $users = User::All();
        return $this->renderHTML('/Entitys/users/usersList.twig', [
            'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
            'users' => $users,
        ]);
    }
    public function deleteAction(ServerRequest $request)
    {        
        $this->userService->deleteUser($request->getQueryParams()['id']);
        return new RedirectResponse('/Intranet/users/list');
    }
}