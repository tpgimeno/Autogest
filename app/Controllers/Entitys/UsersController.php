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
        $this->model = new User();
        $this->route = 'users';
        $this->titleList = 'Usuarios';
        $this->titleForm = 'Usuario';
        $this->labels = $this->userService->getLabelsArray(); 
        $this->itemsList = array('id', 'name', 'email', 'access','phone');
    }

    public function getIndexUsers($request) {
        $values = $this->userService->getUserItemsList();       
        return $this->getBaseIndexAction($request, $this->model, $values);  
    }

    public function getAddUserAction($request) {
        $responseMessage = null;       
        $levels = $this->userService->getAllRegisters(new UserLevel());
        $iterables = ['access' => $levels];
        if ($request->getMethod() == 'POST') {
            $postData = $request->getParsedBody();
            $userValidator = v::key('email', v::stringType()->notEmpty())
                    ->key('password', v::stringType()->notEmpty());            
            try {                
                $userValidator->assert($postData); // true 
            } catch (Exception $e) {
                $responseMessage = $this->errorService->getError($e);
            }
            return $this->getBasePostDataAction($request, $this->model, $iterables, $responseMessage);
        } else {
            return $this->getBaseGetDataAction($request, $this->model, $iterables);
        }        
    }

    public function deleteAction(ServerRequest $request) {
        $params = $request->getQueryParams();
        $this->userService->deleteRegister(new User(), $params['id']);
        return new RedirectResponse('Intranet/users/list?menu=' . $params['menu'] . '&item=' . $params['item']);
    }

}
