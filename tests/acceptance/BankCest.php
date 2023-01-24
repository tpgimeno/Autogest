<?php

namespace Tests\acceptance;

use AcceptanceTester;

class BankCest {

    public function _before(AcceptanceTester $I) {
        LoginTestCest::LoginPassTest($I);
    }

    // tests
    public function banksList(AcceptanceTester $I) {        
        $I->click('Mantenimiento');        
        $I->waitForElementVisible('#banks > a', 2);
        $I->click('Bancos');
        $I->see('Bancos');
        $I->click('#newButton');
        $I->see('Banco');
        $I->click('Lista');
        $I->seeCurrentUrlEquals("/Intranet/banks/list?menu=mantenimiento&item=banks");
    }

}
