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
        $I->click('Bancos');
        $I->see('Bancos');
        $I->click('#newButton');
        $I->see('Banco');
        $I->click('Lista');
        $I->seeCurrentUrlEquals("/Intranet/banks/list?menu=mantenimiento&item=banks");
        $lastRecord = $I->grabNumRecords('banks', ['deleted_at' => null]);        
        $I->click('#editButton' . $lastRecord);
        $I->fillField('#name', 'LoremIpsumTest');
        $I->click('Guardar');
    }
}
