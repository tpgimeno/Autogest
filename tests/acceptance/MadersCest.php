<?php

namespace Tests\acceptance;

use AcceptanceTester;

class MaderCest {    

    public function _before(AcceptanceTester $I) {
        LoginTestCest::LoginPassTest($I);
    }

    // tests
    public function madersList(AcceptanceTester $I) {
        $I->click('Stock');        
        $I->waitForElementVisible('#maders > a', 10);
        $I->click('Fabricantes');
        $I->see('Fabricantes');
        $I->click('#newButton');
        $I->see('fabricantes');
        $I->click('Lista');
        $I->seeCurrentUrlEquals("/Intranet/maders/list?menu=stock&item=maders");
    }

}
