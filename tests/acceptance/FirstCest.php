<?php 

class FirstCest
{
    public function frontpageWorks(AcceptanceTester $I)
    {
        $I->amOnPage('/');        
    }
    public function loginTest(AcceptanceTester $I){
      
        $I->amOnPage('/');
        $I->see('Inicio');
        $I->fillField('email', 'tonyllomouse@gmail.com');
        $I->fillField('password', '12345');
        $I->click('Submit', '#login');
        $I->seeCurrentUrlEquals('/intranet/admin');
    }
}