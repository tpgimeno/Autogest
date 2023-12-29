<?php

namespace Tests\acceptance;

use AcceptanceTester;

class AccountCest {
    
    public $permitted_chars;
    protected $id;
    protected $accountNumber;
    protected $bank;

    public function _before(AcceptanceTester $I) {
        LoginTestCest::LoginPassTest($I);
    }

    // tests
    public function accountsList(AcceptanceTester $I) {        
        $I->click('Mantenimiento');
        $I->waitForElementVisible("#accounts > a", 3);
        $I->click('Cuentas Bancarias');
        $I->see('Cuentas Bancarias');
        $I->click('#newButton');
        $I->see('Cuenta Bancaria');
        $I->click('Lista');
        $I->seeCurrentUrlEquals("/Intranet/accounts/list?menu=mantenimiento&item=accounts");
    }
    
    public function addAccountTest(AcceptanceTester $I) {
        $I->click('Mantenimiento');
        $I->waitForElementVisible("#accounts > a", 3);
        $I->click('Cuentas Bancarias');
        $I->see('Cuentas Bancarias');
        $I->click('#newButton');
        $banks = $I->grabColumnFromDatabase('banks', 'id', ['deleted_at' => null]);
        $I->selectOption('#bank_id', $banks[count($banks) - 1]);  
        $this->permitted_chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';        
        $this->accountNumber = substr(str_shuffle($this->permitted_chars), 0, 20);              
        $I->fillField('#owner', 'LoremIpsum');
        $I->fillField('#accountNumber', $this->accountNumber);
        $I->fillField('observations', 'Lorem ipsum ...');
        $I->click('#submit');
        $I->see('Saved');
        $this->id = $I->grabFromDatabase('accounts', 'id', ['accountNumber' => $this->accountNumber]);
    }

    public function editAccountTest(AcceptanceTester $I) {
        $I->click('Mantenimiento');
        $I->waitForElementVisible("#accounts > a", 3);
        $I->click('Cuentas Bancarias');
        $I->see('Cuentas Bancarias');        
        $I->click('#editButton' . $this->id);
        $this->permitted_chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';        
        $this->accountNumber = substr(str_shuffle($this->permitted_chars), 0, 20);  
        $account = ['bank' => $this->bank, 'owner' => 'LoremIpsum', 'accountNumber' => $this->accountNumber, 'observations' => 'Lorem ipsum ...'];
        $I->submitForm('#formCuentaBancaria', $account);
        $I->see('Updated');
    }

    public function delFromAccountsListTest(AcceptanceTester $I) {
        $I->click('Mantenimiento');
        $I->waitForElementVisible("#accounts > a", 3);
        $I->click('Cuentas Bancarias');
        $I->see('Cuentas Bancarias');       
        $I->click('#delButton' . $this->id);
        $I->dontSeeInDatabase('accounts', array('id' => intval($this->id), 'deleted_at' => null));
    }

    public function delFromAccountFormTest(AcceptanceTester $I) {
        $this->addAccountTest($I);
        $this->_before($I);
        $I->click('Mantenimiento');
        $I->waitForElementVisible("#accounts > a", 3);
        $I->click('Cuentas Bancarias');
        $I->see('Cuentas Bancarias');               
        $I->click('#editButton' . $this->id);
        $I->click('Eliminar');
        $I->dontSeeInDatabase('accounts', array('id' => intval($this->id), 'deleted_at' => null));
    } 

}
