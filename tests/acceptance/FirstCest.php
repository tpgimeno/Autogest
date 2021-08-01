<?php 

class FirstCest
{
    public function frontpageWorks(AcceptanceTester $I)
    {
        $I->amOnPage('/intranet');        
    }
    public function loginTest(AcceptanceTester $I){
      
        $I->amOnPage('/intranet');
        $I->see('Inicio');
        $I->fillField('email', 'tonyllomouse@gmail.com');
        $I->fillField('password', '12345');
        $I->click('Enviar', '.btn');
        $I->seeCurrentUrlEquals('/intranet/admin');
    }
}