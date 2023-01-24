<?php

class FinanceActionsCest {

    public $permitted_chars;
    protected $id;
    protected $fiscalId;

    public function _before(FunctionalTester $I) {
        $I->amOnPage("/");
        $I->submitForm('#loginForm', ['email' => 'tonyllomouse@gmail.com', 'password' => '12345']);
    }

    // tests
    public function addFinanceTest(FunctionalTester $I) {
        $I->amOnPage("/finance/list?menu=mantenimiento&item=finance");
        $I->click('#newButton');
        $this->permitted_chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $this->fiscalId = substr(str_shuffle($this->permitted_chars), 0, 10);
        $bank = $I->grabNumRecords('banks', ['deleted_at' => null]);
        $postalCode = random_int(10000, 99999);
        $financiera = ['name' => 'LoremIpsum', 'fiscalId' => $this->fiscalId, 'bankId' => $bank, 'fiscalName' => 'LoremIpsum SA', 'address' => 'C/ Lorem Ipsum, 123', 'postalCode' => $postalCode, 'city' => 'Ipsum', 'state' => 'Lorem', 'country' => 'IpsumLorem', 'phone' => '962541144', 'email' => 'loremipsum@loremipsum.com', 'site' => 'www.loremipsum.com'];
        $I->submitForm('#formFinanciera', $financiera);
        $this->id = $I->grabFromDatabase('finance', 'id', ['fiscalId' => $this->fiscalId]);
        $I->see('Saved');
    }

    public function editFinanceTest(FunctionalTester $I) {
        $I->amOnPage("/finance/list?menu=mantenimiento&item=finance");
        $I->click('#editButton' . $this->id);
        $bank = $I->grabNumRecords('finance', ['deleted_at' => null]);
        $financiera = ['id' => $this->id, 'name' => 'LoremIpsumEdited', 'fiscalId' => substr(str_shuffle($this->permitted_chars), 0, 10), 'bankId' => $bank, 'fiscalName' => 'LoremIpsum SA', 'address' => 'C/ Lorem Ipsum, 123', 'postalCode' => random_int(10000, 99999), 'city' => 'Ipsum', 'state' => 'Lorem', 'country' => 'IpsumLorem', 'phone' => '962541144', 'email' => 'loremipsum@loremipsum.com', 'site' => 'www.loremipsum.com'];
        $I->submitForm('#formFinanciera', $financiera);
        $I->see('Updated');
    }

    public function delFromFinanceListTest(FunctionalTester $I) {
        $I->amOnPage("/finance/list?menu=mantenimiento&item=finance");
        $I->click('#delButton' . $this->id);
        $I->dontSeeInDatabase('finance', array('id' => intval($this->id), 'deleted_at' => null));        
        
    }

    public function delFromFinanceFormTest(FunctionalTester $I) {
        $I->amOnPage("/finance/list?menu=mantenimiento&item=finance");
        $lastRegister = $I->grabNumRecords('finance', array('deleted_at' => null));
        if($lastRegister === 0){
            $this->addFinanceTest($I);
            $I->click('Lista');
            $lastRegister = $I->grabNumRecords('finance', array('deleted_at' => null));
        }
        $registers = $I->grabColumnFromDatabase('finance', 'id', array('deleted_at' => null));
        $I->click('#editButton' . $registers[$lastRegister - 1]);
        $I->click('Eliminar');
        $I->dontSeeInDatabase('finance', array('id' => intval($registers[$lastRegister - 1]), 'deleted_at' => null));
    }

}
