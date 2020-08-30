<?php

namespace App\Controllers;

require_once "../vendor/autoload.php";

use App\Services\CurrentUserService;
use App\Services\ErrorService;
use Laminas\Diactoros\Response\HtmlResponse;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Extra\Intl\IntlExtension;
use Twig\Loader\FilesystemLoader;




class BaseController
{
    protected $templateEngine;
    protected $currentUser;
    protected $errorService;
    
    public function __construct()   
    {
        $loader = new FilesystemLoader('../views');
        $this->templateEngine = new Environment($loader, [
            'debug' => true,   
            'cache' => false,
        ]);
        $this->currentUser = new CurrentUserService();
        $this->errorService = new ErrorService();
        $this->templateEngine->addExtension(new DebugExtension());
        $this->templateEngine->addExtension(new IntlExtension());      
    }
    public function renderHTML($fileName, $data = [])
    {      
        return new HtmlResponse($this->templateEngine->render($fileName, $data));
    }
}