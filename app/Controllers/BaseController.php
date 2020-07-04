<?php

namespace App\Controllers;

require_once "../vendor/autoload.php";

use App\Services\CurrentUserService;
use Laminas\Diactoros\Response\HtmlResponse;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;




class BaseController
{
    protected $templateEngine;
    protected $currentUser;
    
    public function __construct()   
    {
        $loader = new FilesystemLoader('../views');
        $this->templateEngine = new Environment($loader, [
            'debug' => true,   
            'cache' => false,
        ]);
        $this->currentUser = new CurrentUserService();
        $this->templateEngine->addExtension(new \Twig\Extension\DebugExtension());
        
      
    }
    public function renderHTML($fileName, $data = [])
    {      
        return new HtmlResponse($this->templateEngine->render($fileName, $data));
    }
}