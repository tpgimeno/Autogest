<?php

namespace Tests\acceptance;

use AcceptanceTester;

class StoreCest {

    public function _before(AcceptanceTester $I) {
        LoginTestCest::LoginPassTest($I);
    }

    // tests
    public function storesList(AcceptanceTester $I) {        
        $I->click('Stock');        
        $I->waitForElementVisible('#stores > a', 2);
        $I->click('Almacenes');
        $I->see('Almacenes');
        $I->click('#newButton');
        $I->see('Almacén');
        $I->click('Lista');
        $I->seeCurrentUrlEquals("/Intranet/stores/list?menu=stock&item=stores");
    }

}
