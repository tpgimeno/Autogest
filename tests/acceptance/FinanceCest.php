<?php

namespace Tests\acceptance;

use AcceptanceTester;

class FinanceCest {

    public function _before(AcceptanceTester $I) {
        LoginTestCest::LoginPassTest($I);
    }

    // tests
    public function FinanceList(AcceptanceTester $I) {
        $I->click('Mantenimiento');
        $I->click('Financieras');
        $I->see('Financieras');
        $I->click('#newButton');
        $I->see('Financiera');
        $I->click('Lista');
        $I->seeCurrentUrlEquals("/Intranet/finance/list?menu=mantenimiento&item=finance");
        $lastRecord = $I->grabNumRecords('finance', ['deleted_at' => null]);        
        $I->click('#editButton' . $lastRecord);
        $I->fillField('#name', 'LoremIpsumAcceptanceTest');
        $I->click('Guardar');
    }
    
   
}
