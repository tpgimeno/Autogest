<?php

namespace Tests\acceptance;

use AcceptanceTester;

class CompanyCest {    

    public function _before(AcceptanceTester $I) {
        LoginTestCest::LoginPassTest($I);
    }

    // tests
    public function companiesList(AcceptanceTester $I) {
        $I->click('Mantenimiento');        
        $I->waitForElementVisible('#companies > a', 2);
        $I->click('Empresas');
        $I->see('Empresas');
        $I->click('#newButton');
        $I->see('Empresa');
        $I->click('Lista');
        $I->seeCurrentUrlEquals("/Intranet/company/list?menu=mantenimiento&item=companies");
    }

}
