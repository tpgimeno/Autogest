<?php

class LoginTestCest {

    public function _before(AcceptanceTester $I) {
        
    }

    // tests
    public function LoginAccesTest(AcceptanceTester $I) {
        $I->amGoingTo("Verificar que hay acceso a la página de Login");
        $I->amOnPage("/");
        $I->see("Iniciar sesión");
    }

}
