<?php

use App\Services\AuthService;
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
    public function testPostLoginFailFunction() {
        $arrayPost = ["email" => null, "password" => null];
        $loginService = new AuthService();        
        $this->tester->assertNull($loginService->idUserRegistered($arrayPost));    
    }

}
