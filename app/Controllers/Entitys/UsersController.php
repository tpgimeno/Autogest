<?php

namespace App\Controllers\Entitys;

use App\Controllers\BaseController;
use App\Models\User;
use App\Models\UserLevel;
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
        $levels = $this->userService->getAllRegisters(new UserLevel());        
        return $this->renderHTML('/Entitys/users/usersList.html.twig', [
                    'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
                    'menuState' => $menuState,
                    'menuItem' => $menuItem,
                    'levels' => $levels,
                    'users' => $users
        ]);
    }

    public function getAddUserAction($request) {
        $responseMessage = null;
        $menuState = null;
        $menuItem = null;
        $levels = null;
        if ($request->getMethod() == 'POST') {
            $postData = $request->getParsedBody();
            $userValidator = v::key('email', v::stringType()->notEmpty())
                    ->key('password', v::stringType()->notEmpty());
            try {
                
                $userValidator->assert($postData); // true 
                $response = $this->userService->saveRegister(new User(), $postData);
                $responseMessage = $response[1];
                $userSelected = $this->userService->setInstance(new User(), array('id' => $response[0]));
                $menuState = $postData['menu'];
                $menuItem = $postData['menuItem'];
                $levels = $this->userService->getAllRegisters(new UserLevel());
                
            } catch (Exception $e) {
                $responseMessage = $this->errorService->getError($e);
            }
        } else {
            $params = $request->getQueryParams();
            $levels = $this->userService->getAllRegisters(new UserLevel());
            $userSelected = $this->userService->setInstance(new User(), $params);
            $menuState = $params['menu'];
            $menuItem = $params['menuItem'];
        }
        return $this->renderHTML('/Entitys/users/userForm.html.twig', [
                    'responseMessage' => $responseMessage,
                    'menuState' => $menuState,
                    'menuItem' => $menuItem,
                    'levels' => $levels,
                    'userSelected' => $userSelected
        ]);
    }

    public function deleteAction(ServerRequest $request) {
        $params = $request->getQueryParams();
        $this->userService->deleteRegister(new User(), $params['id']);
        return new RedirectResponse('Intranet/users/list?menu=' . $params['menu'] . '&item=' . $params['item']);
    }

}
