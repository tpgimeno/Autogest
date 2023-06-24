<?php

namespace Tests\acceptance;

use AcceptanceTester;

class LocationCest {

    public function _before(AcceptanceTester $I) {
        LoginTestCest::LoginPassTest($I);
    }

    // tests
    public function LocationList(AcceptanceTester $I) {
        $I->click('Stock');
        $I->waitForElementVisible('#locations > a', 2);
        $I->click('Ubicaciones');
        $I->see('Ubicaciones');
        $I->click('#newButton');
        $I->see('Ubicacion');
        $I->click('Lista');
        $I->seeCurrentUrlEquals("/Intranet/locations/list?menu=stock&item=locations");
        
    }
    
   
}
