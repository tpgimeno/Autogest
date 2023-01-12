<?php

namespace Tests\acceptance;

use AcceptanceTester;

class LoginTestCest {

    public function _before(AcceptanceTester $I) {
        
    }

    // tests    

    public function LoginAccesTest(AcceptanceTester $I) {

        $I->amGoingTo("Verificar que hay acceso a la página de Login");
        $I->amOnPage("/");
        $I->see("Iniciar sesión");
    }

    public static function LoginPassTest(AcceptanceTester $I) {
        
        $I->amOnPage("/");
        $I->submitForm('#loginForm', ['email' => 'tonyllomouse@gmail.com', 'password' => '12345']);
        $I->see('Dashboard');
    }

}
