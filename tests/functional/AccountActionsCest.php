<?php

class AccountActionsCest {

    public $permitted_chars;
    protected $id;
    protected $accountNumber;
    protected $bank;

    public function _before(FunctionalTester $I) {
        $I->amOnPage("/");
        $I->submitForm('#loginForm', ['email' => 'tonyllomouse@gmail.com', 'password' => '12345']);
    }

    // tests
    public function addAccountTest(FunctionalTester $I) {
        $I->amOnPage("/accounts/list?menu=mantenimiento&item=accounts");
        $I->click('#newButton');
        $this->permitted_chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $this->accountNumber = substr(str_shuffle($this->permitted_chars), 0, 20);
        $lastBank = $I->grabNumRecords('banks', array('deleted_at' => null));
        $banks = $I->grabColumnFromDatabase('banks', 'id', array('deleted_at' => null));        
        $this->bank = $banks[$lastBank -1];
        $account = ['bank' => $banks[$lastBank -1], 'owner' => 'LoremIpsum', 'accountNumber' => $this->accountNumber, 'observations' => 'Lorem ipsum ...'];
        $I->submitForm('#formCuentaBancaria', $account);        
        $this->id = $I->grabFromDatabase('accounts', 'id', ['accountNumber' => $this->accountNumber]);        
        $I->see('Saved');
    }

    public function editAccountTest(FunctionalTester $I) {
        $I->amOnPage("/accounts/list?menu=mantenimiento&item=accounts");
        $I->click('#editButton' . $this->id);
        $account = ['bank' => $this->bank, 'owner' => 'LoremIpsum', 'accountNumber' => $this->accountNumber, 'observations' => 'Lorem ipsum ...'];
        $I->submitForm('#formCuentaBancaria', $account);
        $I->see('Updated');
    }

    public function delFromAccountsListTest(FunctionalTester $I) {
        $I->amOnPage("/accounts/list?menu=mantenimiento&item=accounts");
        $I->click('#delButton' . $this->id);
        $I->dontSeeInDatabase('accounts', array('id' => intval($this->id), 'deleted_at' => null));
    }

    public function delFromAccountFormTest(FunctionalTester $I) {
        $I->amOnPage("/accounts/list?menu=mantenimiento&item=accounts");
        $lastRegister = $I->grabNumRecords('accounts', array('deleted_at' => null));  
        if($lastRegister === 0){
            $this->addAccountTest($I);
            $lastRegister = $I->grabNumRecords('accounts', array('deleted_at' => null));  
        }
        $registers = $I->grabColumnFromDatabase('accounts', 'id', array('deleted_at' => null));
        $I->click('#editButton' . $registers[$lastRegister -1]);
        $I->click('Eliminar');
        $I->dontSeeInDatabase('accounts', array('id' => intval($registers[$lastRegister-1]), 'deleted_at' => null));
    }
    
    public function _after(FunctionalTester $I){
        $this->addAccountTest($I);
    }

}
