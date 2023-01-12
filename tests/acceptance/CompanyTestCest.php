<?php

namespace Tests\acceptance;

use AcceptanceTester;

class CompanyTestCest {

    protected $id;

    public function _before(AcceptanceTester $I) {
        LoginTestCest::LoginPassTest($I);
    }

    // tests
    public function accessCompaniesList(AcceptanceTester $I) {
        $I->click('Mantenimiento');
        $I->click('Empresas');
        $I->see('Empresas');
    }

    public function newCompanyTest(AcceptanceTester $I) {
        $I->amOnPage("/company/list?menu=mantenimiento&item=companies");
        $I->click('#newButton');
        $I->see('Empresa');
    }

    public function returnCompaniesList(AcceptanceTester $I) {
        $I->click('Mantenimiento');
        $I->click('Empresas');
        $I->click('#newButton');
        $I->click('Lista');
        $I->seeCurrentUrlEquals("/Intranet/company/list?menu=mantenimiento&item=companies");
    }

}
