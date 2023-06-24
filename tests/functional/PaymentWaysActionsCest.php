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
        $I->amOnPage("/paymentWays/list?menu=compras&item=paymentWays");
        $I->click('#newButton');
        $this->permitted_chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $this->name = substr(str_shuffle($this->permitted_chars), 0, 20);        
        $paymentWay = ['name' => $this->name, 'accountAssociated' => '1', 'discount' => '20'];
        $I->submitForm('#formFormadePago', $paymentWay);        
        $this->id = $I->grabFromDatabase('paymentWays', 'id', ['name' => 'Transferencia Bancaria']);        
        $I->see('Saved');
    }

    public function editPaymentWaysTest(FunctionalTester $I) {
        $I->amOnPage("/paymentWays/list?menu=compras&item=buypaymentWays");
        $I->click('#editButton' . $this->id);
        $paymentWay = ['name' => $this->name, 'accountAssociated' => '1', 'discount' => '20'];
        $I->submitForm('#formFormadePago', $paymentWay);
        $I->see('Updated');
    }

    public function delFromPaymentWayssListTest(FunctionalTester $I) {
        $I->amOnPage("/paymentWays/list?menu=compras&item=paymentWays");
        $I->click('#delButton' . $this->id);
        $I->dontSeeInDatabase('paymentWays', array('id' => intval($this->id), 'deleted_at' => null));
    }

    public function delFromPaymentWaysFormTest(FunctionalTester $I) {
        $I->amOnPage("/paymentWays/list?menu=compras&item=paymentWays");
        $lastRegister = $I->grabNumRecords('paymentWays', array('deleted_at' => null));  
        if($lastRegister === 0){
            $this->addPaymentWaysTest($I);
            $lastRegister = $I->grabNumRecords('paymentWays', array('deleted_at' => null));  
        }
        $registers = $I->grabColumnFromDatabase('paymentWays', 'id', array('deleted_at' => null));
        $I->click('#editButton' . $registers[$lastRegister -1]);
        $I->click('Eliminar');
        $I->dontSeeInDatabase('paymentWays', array('id' => intval($registers[$lastRegister-1]), 'deleted_at' => null));
    }
    
    public function _after(FunctionalTester $I){
        $this->addPaymentWaysTest($I);
    }

}
