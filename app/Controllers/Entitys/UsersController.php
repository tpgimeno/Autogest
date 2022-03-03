<?php

namespace App\Controllers\Entitys;

use App\Controllers\BaseController;
use App\Models\User;
use App\Services\Entitys\UserService;
use Exception;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Diactoros\ServerRequest;
use Respect\Validation\Validator as v;

class UsersController extends BaseController {
    protected $userService;
    protected $list = '/Intranet/users/list';
    protected $tab = 'home';
    protected $title = 'Usuarios';
    protected $save = "/Intranet/users/form";
    protected $formName = "userForm";    
    protected $inputs = ['id' => ['id' => 'inputID', 'name' => 'id', 'title' => 'ID'],       
        'email' => ['id' => 'inputEmail', 'name' => 'email', 'title' => 'Email'],
        'password' => ['id' => 'inputPassword', 'name' => 'password', 'title' => 'Password']];
    public function __construct(UserService $userService) {
        parent::__construct();
        $this->userService = $userService;
    }
    public function getIndexUsers() {
        $users = $this->userService->getAllRegisters(new User());
        return $this->renderHTML('/Entitys/users/usersList.html.twig', [
            'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
            'tab' => $this->tab,
            'users' => $users
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
        $selected_user = $this->userService->setInstance(new User(), $request->getQueryParams('id'));        
        return $this->renderHTML('/Entitys/users/userForm.html.twig', [
            'responseMessage' => $responseMessage,
            'list' => $this->list,
            'save' => $this->save,
            'tab' => $this->tab,
            'title' => $this->title,
            'inputs' => $this->inputs,
            'value' => $selected_user
        ]);         
    }    
    public function deleteAction(ServerRequest $request) {        
        $this->userService->deleteRegister(new User(), $request->getQueryParams('id'));
        return new RedirectResponse($this->list);
    }
}