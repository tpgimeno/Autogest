<?php

class BankActionsCest {

    public $permitted_chars;
    protected $id;
    protected $fiscalId;

    public function _before(FunctionalTester $I) {
        $I->amOnPage("/");
        $I->submitForm('#loginForm', ['email' => 'tonyllomouse@gmail.com', 'password' => '12345']);
    }

    // tests
    public function addBankTest(FunctionalTester $I) {
        $I->amOnPage("/banks/list?menu=mantenimiento&item=banks");
        $I->click('#newButton');
        $this->permitted_chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $this->fiscalId = substr(str_shuffle($this->permitted_chars), 0, 10);
        $bankCode = rand(1000, 9999);
        $postalCode = random_int(10000, 99999);
        $banco = ['name' => 'LoremIpsum', 'bankCode' => $bankCode, 'fiscalId' => $this->fiscalId, 'fiscalName' => 'LoremIpsum SA', 'address' => 'C/ Lorem Ipsum, 123', 'postalCode' => $postalCode, 'city' => 'Ipsum', 'state' => 'Lorem', 'country' => 'IpsumLorem', 'phone' => '962541144', 'email' => 'loremipsum@loremipsum.com', 'site' => 'www.loremipsum.com'];
        $I->submitForm('#formBanco', $banco);
        $this->id = $I->grabFromDatabase('banks', 'id', ['fiscalId' => $this->fiscalId]);
        $I->see('Saved');
    }

    public function editBankTest(FunctionalTester $I) {
        $I->amOnPage("/banks/list?menu=mantenimiento&item=banks");
        $I->click('#editButton' . $this->id);
        $banco = ['id' => $this->id, 'name' => 'LoremIpsumEdited', 'bankCode' => rand(1000, 9999), 'fiscalId' => substr(str_shuffle($this->permitted_chars), 0, 10), 'fiscalName' => 'LoremIpsum SA', 'address' => 'C/ Lorem Ipsum, 123', 'postalCode' => random_int(10000, 99999), 'city' => 'Ipsum', 'state' => 'Lorem', 'country' => 'IpsumLorem', 'phone' => '962541144', 'email' => 'loremipsum@loremipsum.com', 'site' => 'www.loremipsum.com'];
        $I->submitForm('#formBanco', $banco);
        $I->see('Updated');
    }

    public function delFromBankListTest(FunctionalTester $I) {
        $I->amOnPage("/banks/list?menu=mantenimiento&item=banks");
        $I->click('#delButton' . $this->id);
        $I->dontSeeInDatabase('banks', array('id' => intval($this->id), 'deleted_at' => null));
    }

    public function delFromBankFormTest(FunctionalTester $I) {
        $this->addBankTest($I);
        $this->_before($I);
        $I->amOnPage("/banks/list?menu=mantenimiento&item=banks");        
        $I->click('#editButton' . $this->id);
        $I->click('Eliminar');
        $I->dontSeeInDatabase('banks', array('id' => intval($this->id), 'deleted_at' => null));
    }
}
