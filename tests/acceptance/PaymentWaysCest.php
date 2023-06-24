<?php

namespace Tests\acceptance;

use AcceptanceTester;

class PaymentWaysCest {

    public function _before(AcceptanceTester $I) {
        LoginTestCest::LoginPassTest($I);
    }

    // tests
    public function paymentWaysList(AcceptanceTester $I) {        
        $I->click('Compras');
        $I->waitForElementVisible("#buyPaymentWays > a", 3);
        $I->click('Formas de Pago');
        $I->see('Formas de Pago');
        $I->click('#newButton');
        $I->see('Forma de Pago');
        $I->click('Lista');
        $I->seeCurrentUrlEquals("/Intranet/paymentWays/list?menu=compras&item=paymentWays");
    }

}
