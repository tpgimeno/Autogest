<?php

class LoginFunctionalTestCest
{
    public function _before(FunctionalTester $I)
    {
        
    }

    // tests
    public function LoginFailTest(FunctionalTester $I) {
        $I->amOnPage("/");
        $I->see("Iniciar sesión");
        $I->submitForm('#loginForm', ['email' => 'tony@hotmail.com', 'password' => '435665']);
        $I->see('El usuario o el Password no es correcto');
    }

    public function LoginPassTest(FunctionalTester $I) {
        $I->amOnPage("/");
        $I->see("Iniciar sesión");
        $I->submitForm('#loginForm', ['email' => 'tonyllomouse@gmail.com', 'password' => '12345']);        
        $I->canSee("Mantenimiento");
        
    }
}
