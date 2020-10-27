<?php 

class homeCest
{
    public function _before(FunctionalTester $I)
    {
    }

    // tests
    public function loginTest(FunctionalTester $I){
      
        $I->amOnPage('/');
        $I->see('Inicio');
        $I->fillField('email', 'tonyllomouse@gmail.com');
        $I->fillField('password', '12345');
        $I->click('Submit', '#login');
        $I->seeCurrentUrlEquals('/intranet/admin');
    }
}
