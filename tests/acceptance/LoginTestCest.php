<?php

use App\Models\User;

class LoginTestCest {

    protected $user;

    public function _before(AcceptanceTester $I) {        
                
    }

    // tests
    public function LoginFailTest(AcceptanceTester $I) {
        $I->amOnPage("/");
        $I->see("Iniciar sesión");
        $I->submitForm('#loginForm', ['email' => 'tony@hotmail.com', 'password' => '435665']);
        $I->see('El usuario o el Password no es correcto');
    }

    public function LoginPassTest(AcceptanceTester $I) {
        $I->amOnPage("/");
        $I->see("Iniciar sesión");
        $I->submitForm('#loginForm', ['email' => 'tonyllomouse@gmail.com', 'password' => '12345']);
        $I->amOnPage("/admin");
        $I->click("Empresas");
    }

}
