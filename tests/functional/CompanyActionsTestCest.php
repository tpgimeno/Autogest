<?php

class CompanyActionsTestCest
{
    public function _before(FunctionalTester $I)
    {
        $I->amOnPage("/");
        $I->see("Iniciar sesión");
        $I->submitForm('#loginForm', ['email' => 'tonyllomouse@gmail.com', 'password' => '12345']); 
        $I->amGoingTo("Valido la página en que me encuentro");
        
        $I->canSee("Dashboard");
    }

    // tests
    public function tryToTest(FunctionalTester $I)
    {
          
    }
}
