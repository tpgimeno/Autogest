<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace App\Middlewares;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\EmptyResponse;

class AuthenticationMiddleware implements MiddlewareInterface
{   
/**
 *Process an incoming Server request.
 * With this middleware we verify the authentication of users before init the aplication
 *  
 **/
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface 
    {
        if($request->getUri()->getPath() === '/Intranet/admin')
        {            
            $cookies = $request->getCookieParams();           
            $sessionUserId = $cookies['userId'] ?? null;            
            if(!$sessionUserId)
            {
                return new EmptyResponse(401);
            }            
        }              
        return $handler->handle($request);       
    }
}