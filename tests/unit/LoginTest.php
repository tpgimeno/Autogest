<?php

use App\Controllers\AuthController;
use Codeception\Test\Unit;

class LoginTest extends Unit {

    /**
     * @var UnitTester
     */
    protected $tester;

    protected function _before() {
        
    }

    protected function _after() {
        
    }

    // tests
    public function testPostLoginFunction() {
        $arrayPost = ["email" => null, "password" => null];
        $new_request = new \Laminas\Diactoros\ServerRequest();
        
        $post = new AuthController();
        $post->postLogin($request)
    }

}
