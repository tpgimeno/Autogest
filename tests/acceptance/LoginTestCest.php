<?php

class LoginTestCest {

    public function _before(AcceptanceTester $I) {
        
    }

    // tests
    public function LoginAccesTest(AcceptanceTester $I) {
        $I->amOnPage("/");
        $I->see("Iniciar sesión");
    }

}
