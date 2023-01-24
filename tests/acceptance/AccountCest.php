<?php

namespace Tests\acceptance;

use AcceptanceTester;

class AccountCest {

    public function _before(AcceptanceTester $I) {
        LoginTestCest::LoginPassTest($I);
    }

    // tests
    public function accountsList(AcceptanceTester $I) {        
        $I->click('Mantenimiento');
        $I->waitForElementVisible("#accounts > a", 3);
        $I->click('Cuentas Bancarias');
        $I->see('Cuentas Bancarias');
        $I->click('#newButton');
        $I->see('Cuenta Bancaria');
        $I->click('Lista');
        $I->seeCurrentUrlEquals("/Intranet/accounts/list?menu=mantenimiento&item=accounts");
    }

}
