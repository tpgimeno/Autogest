<?php

namespace App\Controllers;


use Laminas\Diactoros\ServerRequest;

/**
 * @Title INTRANET SELL VEHICLES
 * @Author  Tony Pinto
 */
class IndexController extends BaseController {

    public function indexAction(ServerRequest $request) {
        $cookies = $request->getCookieParams();
        return $this->renderHTML('login.html.twig', [
            'cookies' => $cookies
        ]);
    }

}
