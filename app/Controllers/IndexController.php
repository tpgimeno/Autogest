<?php

namespace App\Controllers;
/**
 * @Title INTRANET SELL VEHICLES
 * @Author  Tony Pinto
 */
class IndexController extends BaseController
{
    public function indexAction()
    {        
        return $this->renderHTML('login.twig'); 
    }     
}