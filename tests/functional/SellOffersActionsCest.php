<?php

class SellOffersActionsCest
{
    protected $id, $permitted_chars;
     
    public function _before(FunctionalTester $I) {
        $I->amOnPage("/");
        $I->submitForm('#loginForm', ['email' => 'tonyllomouse@gmail.com', 'password' => '12345']);
    }

    // tests
    public function addSellOfferTest(FunctionalTester $I) {
        $I->amOnPage("/sales/offers/list?menu=stock&item=offers");
        $I->click('#newButton');
        $this->permitted_chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $tax = $I->grabColumnFromDatabase('taxes', 'id', ['deleted_at' => null]);
        $paymentWay = $I->grabColumnFromDatabase('paymentWays', 'id', ['deleted_at' => null]);
        $customer = $I->grabColumnFromDatabase('customers', 'id', ['deleted_at' => null]);
        $lastOffers = $I->grabColumnFromDatabase('selloffers', 'offerNumber', []); 
        $numberOffer = intval(substr($lastOffers[count($lastOffers) - 1], 2, strlen($lastOffers[count($lastOffers) - 1])))+1;
        $offerNumber = 'OV' . strval($numberOffer);
        $selloffer = ['offerNumber' => $offerNumber, 
            'offerDate' => '12/12/2023', 
            'taxes_id' => $tax[count($tax) - 1], 
            'paymentWays_id' => $paymentWay[count($paymentWay) - 1],
            'customer_id' => $customer[count($customer) - 1],
            'pvp' => 0, 'discount' => 0, 'tva' => 0, 'total' => 0];
        $I->submitForm('#formOfertadeVenta', $selloffer);        
        $this->id = $I->grabFromDatabase('selloffers', 'id', ['offerNumber' => $offerNumber, 'deleted_at' => null]);        
        $I->seeInDatabase('selloffers', ['offerNumber' => $offerNumber]);
    }

    public function editSellOfferTest(FunctionalTester $I) {
        $I->amOnPage("/sales/offers/list?menu=stock&item=offers");
        $I->click('#editButton' . $this->id);        
        $selloffer = ['offerDate' => '12/12/2021'];
        $I->submitForm('#formOfertadeVenta', $selloffer);    
        $I->see('Updated');
    }

    public function delFromSellOffersListTest(FunctionalTester $I) {
        $I->amOnPage("/sales/offers/list?menu=stock&item=offers");
        $I->click('#delButton' . $this->id);
        $I->dontSeeInDatabase('selloffers', array('id' => intval($this->id), 'deleted_at' => null));
    }

    public function delFromSellOfferFormTest(FunctionalTester $I) {
        $this->addSellOfferTest($I);
        $this->_before($I);
        $I->amOnPage("/sales/offers/list?menu=stock&item=offers");        
        $I->click('#editButton' . $this->id);
        $I->click('Eliminar');
        $I->dontSeeInDatabase('selloffers', array('id' => intval($this->id), 'deleted_at' => null));
    }
}
