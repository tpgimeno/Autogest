<?php

namespace App\Controllers\Entitys;

use App\Controllers\BaseController;
use App\Models\User;
use App\Services\Entitys\UserService;
use Exception;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Diactoros\ServerRequest;
use Respect\Validation\Validator as v;

class UsersController extends BaseController 
{
    protected $userService;
    public function __construct(UserService $userService) {
        parent::__construct();
        $this->userService = $userService;
    }
    public function getIndexUsers() {
        $users = $this->userService->getAllRegisters(new User());
        return $this->renderHTML('/Entitys/users/usersList.html.twig', [
            'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
            'users' => $users,
        ]);
    }
    public function getAddUserAction($request){
        $responseMessage = null;
        if($request->getMethod() == 'POST') {
            $postData = $request->getParsedBody();
            $userValidator = v::key('email', v::stringType()->notEmpty()) 
            ->key('password', v::stringType()->notEmpty());            
            try {
                $userValidator->assert($postData); // true 
                $responseMessage = $this->userService->saveRegister(new User(), $postData);              
            }catch(Exception $e){                
                $responseMessage = $this->errorService->getError($e);
            }              
        }    
        $selected_user = null;
        if($request->getQueryParams('id')) {
            $selected_user = $this->userService->setInstance(new User(), $request->getQueryParams());
        }
        return $this->renderHTML('/Entitys/users/userForm.html.twig', [
            'responseMessage' => $responseMessage,
            'user' => $selected_user
        ]);         
    }
    
    public function deleteAction(ServerRequest $request)
    {        
        $this->userService->deleteRegister(new User(), $request->getQueryParams('id'));
        return new RedirectResponse('/Intranet/users/list');
    }
}