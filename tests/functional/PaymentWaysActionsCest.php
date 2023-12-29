<?php

class PaymentWaysActionsCest {

    public $permitted_chars;
    protected $id;  
    protected $name;
   

    public function _before(FunctionalTester $I) {
        $I->amOnPage("/");
        $I->submitForm('#loginForm', ['email' => 'tonyllomouse@gmail.com', 'password' => '12345']);
    }

    // tests
    public function addPaymentWaysTest(FunctionalTester $I) {
        $I->amOnPage("/paymentWays/list?menu=compras&item=buypaymentWays");
        $I->click('#newButton');
        $this->permitted_chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $this->name = substr(str_shuffle($this->permitted_chars), 0, 20);
        $accounts = $I->grabColumnFromDatabase('accounts', 'id', ['deleted_at' => null]);
        $paymentWay = ['name' => $this->name, $accounts[count($accounts) - 1], 'discount' => '20'];
        $I->submitForm('#formFormadePago', $paymentWay);        
        $this->id = $I->grabFromDatabase('paymentWays', 'id', ['name' => $this->name]);        
        $I->see('Saved');
    }

    public function editPaymentWaysTest(FunctionalTester $I) {
        $I->amOnPage("/paymentWays/list?menu=compras&item=buypaymentWays");
        $I->click('#editButton' . $this->id);
        $paymentWay = ['name' => $this->name, 'account_id' => '1', 'discount' => '20'];
        $I->submitForm('#formFormadePago', $paymentWay);
        $I->see('Updated');
    }

    public function delFromPaymentWayssListTest(FunctionalTester $I) {
        $I->amOnPage("/paymentWays/list?menu=compras&item=buypaymentWays");
        $I->click('#delButton' . $this->id);
        $I->dontSeeInDatabase('paymentWays', array('id' => intval($this->id), 'deleted_at' => null));
    }

    public function delFromPaymentWaysFormTest(FunctionalTester $I) {
        $this->addPaymentWaysTest($I);
        $this->_before($I);
        $I->amOnPage("/paymentWays/list?menu=compras&item=buypaymentWays");
        $I->click('#editButton' . $this->id);
        $I->click('Eliminar');
        $I->dontSeeInDatabase('paymentWays', array('id' => intval($this->id), 'deleted_at' => null));
    }
}
