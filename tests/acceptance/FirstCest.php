<?php 
namespace Tests\acceptance;

use AcceptanceTester;

class FirstCest
{
    public function frontpageWorks(AcceptanceTester $I) {
        $I->amOnPage('/Intranet');        
    }
    public function loginTest(AcceptanceTester $I){      
        $I->amOnPage('/Intranet');
        $I->see('Inicio');
        $I->fillField('email', 'tonyllomouse@gmail.com');
        $I->fillField('password', '12345');
        $I->click('Enviar', '.btn');
        $I->seeCurrentUrlEquals('/Intranet/admin');
    }
}