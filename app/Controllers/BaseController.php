<?php

/*
 * Template for all controllers that implements Views engine
 */

namespace App\Controllers;

require_once "../vendor/autoload.php";

use App\Services\BaseService;
use App\Services\CurrentUserService;
use App\Services\ErrorService;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Diactoros\ServerRequest;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Extra\Intl\IntlExtension;
use Twig\Loader\FilesystemLoader;

class BaseController {

    protected $templateEngine;
    protected $currentUser;
    protected $baseService;
    protected $errorService;
    protected $labels;
    protected $itemsList, $labelsForm, $titleList, $titleForm, $properties, $route, $model, $assetsFunction, $assetsNames;

    public function __construct() {
        $loader = new FilesystemLoader('../views');
        $this->templateEngine = new Environment($loader, [
            'debug' => true,
            'cache' => false,
        ]);
        $this->currentUser = new CurrentUserService();
        $this->errorService = new ErrorService();
        $this->templateEngine->addExtension(new DebugExtension());
        $this->templateEngine->addExtension(new IntlExtension());
        $this->baseService = new BaseService();
    }

    public function renderHTML($fileName, $data = []) {
        return new HtmlResponse($this->templateEngine->render($fileName, $data));
    }
    
    public function getBaseIndexAction(ServerRequest $request, $model, $values) {
        $params = $request->getQueryParams();        
        $menuState = $params['menu'];  
        $menuItem = $params['item'];
//        var_dump($values);die();
        if(!$values){
            $values = $this->baseService->getAllRegisters(new $model);
            
        }
        return $this->renderHTML('/templateListView.html.twig', [
                    'values' => $values,
                    'route' => $this->route,
                    'titleList' => $this->titleList,
                    'itemsList' => $this->itemsList,
                    'labels' => $this->labels,                    
                    'menuState' => $menuState,
                    'menuItem' => $menuItem
        ]);
    }
    
    public function getBasePostDataAction($request, $model, $iterables, $responseMessage) {        
        if(is_object($request)){
            $postData = $request->getParsedBody(); 
        }else{
            $postData = $request;
        }
        if(isset($postData['plate']) and $postData['menuItem'] === 'offers'){
            unset($postData['brand'], $postData['model'], $postData['vin'], $postData['km']);
            $postDataInitial = array_slice($postData, 0, (array_search('texts',array_keys($postData)))+1);
            $temp = array_slice($postData, (array_search('texts',array_keys($postData)))+1, count($postData));
            $postDataInitial = array_merge($postDataInitial, ['vehicle_id' => $postData['plate']]);     
            unset($temp['plate']);
            $postData = array_merge($postDataInitial, $temp);
        }
//        var_dump($postData);die();
        $response = $this->baseService->saveRegister(new $model, $postData);        
        if($response){
            $responseMessage = $response[1];
        }
        $valueSelected = $this->baseService->setInstance(new $model, array('id' => $response[0]));        
        $selectedTab = null;
        if(isset($postData['selected_tab'])){
            $selectedTab = $postData['selected_tab'];
        }else{
            $selectedTab = 'data';
        }
        
        $menuState = $postData['menu'];
        $menuItem = $postData['menuItem'];
        $this->properties = $this->baseService->getModelProperties(new $model);        
        return $this->renderHTML('templateFormView.html.twig', [
                    'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
                    'responseMessage' => $responseMessage,                    
                    'value' => $valueSelected,
                    'labels' => $this->labels,
                    'optionsArray' => $iterables,
                    'menuState' => $menuState,
                    'menuItem' => $menuItem,
                    'properties' => $this->properties,
                    'titleForm' => $this->titleForm,
                    'route' => $this->route,
                    'selected_tab' => $selectedTab
        ]);
    }
    
    public function getBaseGetDataAction(ServerRequest $request, $model, $iterables) {           
        $params = $request->getQueryParams(); 
//        var_dump($params);die();
        $valueSelected = $this->baseService->setInstance(new $model, $params);
        $responseMessage = null;
        if(isset($params['responseMessage'])){
            $responseMessage = $params['responseMessage'];
        }        
        $menuState = $params['menu'];
        $menuItem = $params['item'];
        $selectedTab = null;
        if(isset($params['selected_tab'])){
            $selectedTab = $params['selected_tab'];
        }else{
            $selectedTab = 'data';
        }
        $this->properties = $this->baseService->getModelProperties(new $model);             
        return $this->renderHTML('templateFormView.html.twig', [
                    'userEmail' => $this->currentUser->getCurrentUserEmailAction(), 
                    'responseMessage' => $responseMessage,
                    'value' => $valueSelected,
                    'labels' => $this->labels,
                    'optionsArray' => $iterables,
                    'menuState' => $menuState,
                    'menuItem' => $menuItem,
                    'properties' => $this->properties,
                    'titleForm' => $this->titleForm,
                    'selected_tab' => $selectedTab,
                    'route' => $this->route                    
        ]);
    }
    
    public function deleteItemAction(ServerRequest $request, $model){
        $params = $request->getQueryParams();
        $this->baseService->deleteRegister($model, $params);
        $responseMessage = 'Deleted';
        return new RedirectResponse('/Intranet/' . $this->route . '/list?menu=' . $params['menu'] . '&item=' . $params['item'] . '&responseMessage=' . $responseMessage);
    }

    function tofloat($num) {
        $dotPos = strrpos($num, ',');
        $commaPos = strrpos($num, '.');
        $sep = (($dotPos > $commaPos) && $dotPos) ? $dotPos :
                ((($commaPos > $dotPos) && $commaPos) ? $commaPos : false);

        if (!$sep) {
            return floatval(preg_replace("/[^0-9]/", "", $num));
        }

        return floatval(
                preg_replace("/[^0-9]/", "", substr($num, 0, $sep)) . '.' .
                preg_replace("/[^0-9]/", "", substr($num, $sep + 1, strlen($num)))
        );
    }

}
