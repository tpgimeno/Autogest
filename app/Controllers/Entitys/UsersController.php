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

    public function __construct(UserService $userService) {
        parent::__construct();
        $this->userService = $userService;
    }

    public function getIndexUsers($request) {
        $users = $this->userService->getAllRegisters(new User());
        $params = $request->getQueryParams();
        $menuState = $params['menu'];
        $menuItem = $params['item'];
        return $this->renderHTML('/Entitys/users/usersList.html.twig', [
                    'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
                    'menuState' => $menuState,
                    'menuItem' => $menuItem,
                    'users' => $users
        ]);
    }

    public function getAddUserAction($request) {
        $responseMessage = null;
        $menuState = null;
        $menuItem = null;
        if ($request->getMethod() == 'POST') {
            $postData = $request->getParsedBody();
            $userValidator = v::key('email', v::stringType()->notEmpty())
                    ->key('password', v::stringType()->notEmpty());
            try {
                var_dump($postData);die();
                $userValidator->assert($postData); // true 
                $responseMessage = $this->userService->saveRegister(new User(), $postData);
                $menuState = $postData['menu'];
                $menuItem = $postData['menuItem'];
            } catch (Exception $e) {
                $responseMessage = $this->errorService->getError($e);
            }
        }else{
            $params = $request->getQueryParams();
            $userSelected = $this->userService->setInstance(new User(), $params);            
            $menuState = $params['menu'];
            $menuItem = $params['item'];            
        }        
        return $this->renderHTML('/Entitys/users/userForm.html.twig', [
                    'responseMessage' => $responseMessage,
                    'menuState' => $menuState,
                    'menuItem' => $menuItem,
                    'userSelected' => $userSelected
        ]);
    }

    public function deleteAction(ServerRequest $request) {
        $this->userService->deleteRegister(new User(), $request->getQueryParams('id'));
        return new RedirectResponse($this->list);
    }

}
