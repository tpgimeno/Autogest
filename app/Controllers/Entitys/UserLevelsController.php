<?php

namespace App\Controllers\Entitys;

use App\Controllers\BaseController;
use App\Models\User;
use App\Models\UserLevel;
use App\Services\Entitys\UserLevelsService;
use Exception;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Diactoros\ServerRequest;
use Respect\Validation\Validator as v;

class UserLevelsController extends BaseController {

    protected $userLevelsService;

    public function __construct(UserLevelsService $userLevelsService) {
        parent::__construct();
        $this->userLevelsService = $userLevelsService;
    }

    public function getIndexUserLevels($request) {
        $userLevels = $this->userLevelsService->getAllRegisters(new UserLevel());
        $params = $request->getQueryParams();        
        $menuState = $params['menu'];
        $menuItem = $params['item'];             
        return $this->renderHTML('/Entitys/userLevels/userLevelsList.html.twig', [                    
                    'menuState' => $menuState,
                    'menuItem' => $menuItem,
                    'userLevels' => $userLevels                    
        ]);
    }

    public function getAddUserLevelsAction($request) {
        $responseMessage = null;
        $menuState = null;
        $menuItem = null;
        $userLevelSelected = null;
        if ($request->getMethod() == 'POST') {
            $postData = $request->getParsedBody();
            $userLevelValidator = v::key('name', v::stringType()->notEmpty());
            try {                
                $userLevelValidator->assert($postData); // true
                
                $response = $this->userLevelsService->saveRegister(new UserLevel(), $postData);
                $responseMessage = $response[1];
                $userLevelSelected = $this->userLevelsService->setInstance(new UserLevel(), array('id' => $response[0]));
                $menuState = $postData['menu'];
                $menuItem = $postData['menuItem'];
            } catch (Exception $e) {
                $responseMessage = $this->errorService->getError($e);
            }
        } else {
            $params = $request->getQueryParams();
            $userLevelSelected = $this->userLevelsService->setInstance(new UserLevel(), $params);
            $menuState = $params['menu'];
            $menuItem = $params['menuItem'];
            
        }
        return $this->renderHTML('/Entitys/userLevels/userLevelsForm.html.twig', [
                    'responseMessage' => $responseMessage,
                    'menuState' => $menuState,
                    'menuItem' => $menuItem,                    
                    'userLevelSelected' => $userLevelSelected
        ]);
    }

    public function deleteAction(ServerRequest $request) {
        $params = $request->getQueryParams();
        $this->userLevelsService->deleteRegister(new UserLevel(), $params['id']);
//        return new RedirectResponse('Intranet/userLevels/list?menu=' . $params['menu'] . '&item=' . $params['item']);
    }

}
