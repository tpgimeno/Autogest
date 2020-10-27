<?php 

class dashboardTestCest
{
    public function _before(FunctionalTester $I)
    {
        homeCest::loginTest($I);
    }

    // tests
    public function tryToTest(FunctionalTester $I)
    {
        $I->amOnPage('/intranet/admin');
        $I->see('Preferencias');
        
    }
}
