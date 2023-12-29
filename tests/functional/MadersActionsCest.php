<?php

class MadersActionsTestCest {

    public $permitted_chars;
    protected $fiscalId;
    protected $id;
    
    public function _before(FunctionalTester $I) {
        $I->amOnPage("/");
        $I->submitForm('#loginForm', ['email' => 'tonyllomouse@gmail.com', 'password' => '12345']);
    }

    // tests
    public function addMaderTest(FunctionalTester $I) {
        $I->amOnPage("/maders/list?menu=stock&item=maders");
        $I->click('#newButton');
        $this->permitted_chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $this->fiscalId = substr(str_shuffle($this->permitted_chars), 0, 10);
        $fabricante = ['name' => 'LoremIpsum', 'fiscalId' => $this->fiscalId, 'fiscalName' => 'LoremIpsum SA', 'address' => 'C/ Lorem Ipsum, 123', 'postalCode' => random_int(10000, 99999), 'city' => 'Ipsum', 'state' => 'Lorem', 'country' => 'IpsumLorem', 'phone' => '962541144', 'email' => 'loremipsum@loremipsum.com', 'site' => 'www.loremipsum.com'];
        $I->submitForm('#formFabricante', $fabricante);
        $this->id = $I->grabFromDatabase('maders', 'id', ['fiscalId' => $this->fiscalId]);        
        $I->see('Saved');
    }

    public function editMaderTest(FunctionalTester $I) {
        $I->amOnPage("/maders/list?menu=stock&item=maders");        
        $I->click('#editButton' . $this->id);
        $fabricante = ['id' => $this->id, 'name' => 'LoremIpsumEdited', 'fiscalId' => substr(str_shuffle($this->permitted_chars), 0, 10), 'fiscalName' => 'LoremIpsum SA', 'address' => 'C/ Lorem Ipsum, 123', 'postalCode' => random_int(10000, 99999), 'city' => 'Ipsum', 'state' => 'Lorem', 'country' => 'IpsumLorem', 'phone' => '962541144', 'email' => 'loremipsum@loremipsum.com', 'site' => 'www.loremipsum.com'];
        $I->submitForm('#formFabricante', $fabricante);
        $I->see('Updated');        
    }
    
    public function delFromMaderListTest(FunctionalTester $I) {
        $I->amOnPage("/maders/list?menu=stock&item=maders");
        $I->click('#delButton' . $this->id);
        $I->dontSeeInDatabase('maders', array('id' => intval($this->id), 'deleted_at' => null));        
    }
    
    public function delFromMaderFormTest(FunctionalTester $I) {
        $this->addMaderTest($I);
        $this->_before($I);
        $I->amOnPage("/maders/list?menu=stock&item=maders");               
        $I->click('#editButton' . $this->id);
        $I->click('Eliminar');
        $I->dontSeeInDatabase('maders', array('id' => intval($this->id), 'deleted_at' => null));
    }
}
