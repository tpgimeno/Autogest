<?php

class AccesoriesActionsCest
{
    public $permitted_chars;
    protected $id;
    protected $name;
    
    public function _before(FunctionalTester $I) {
        $I->amOnPage("/");
        $I->submitForm('#loginForm', ['email' => 'tonyllomouse@gmail.com', 'password' => '12345']);
    }

    // tests
    public function addAccesoryTest(FunctionalTester $I) {
        $I->amOnPage("/vehicles/accesories/list?menu=stock&item=accesories");
        $I->click('#newButton');
        $this->permitted_chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $this->name = substr(str_shuffle($this->permitted_chars), 0, 12);
        $key = substr(str_shuffle($this->permitted_chars), 0, 20);
        $accesory = ['name' => $this->name, 'keyString' => $key];
        $I->submitForm('#formAccesorio', $accesory);        
        $this->id = $I->grabFromDatabase('accesories', 'id', ['name' => $this->name]);        
        $I->seeInDatabase('accesories', ['keyString' => $key]);
    }

    public function editAccesoryTest(FunctionalTester $I) {
        $I->amOnPage("/vehicles/accesories/list?menu=stock&item=accesories");
        $I->click('#editButton' . $this->id);        
        $key = substr(str_shuffle($this->permitted_chars), 0, 20);
        $accesory = ['name' => $this->name, 'keyString' => $key];
        $I->submitForm('#formAccesorio', $accesory);
        $I->see('Updated');
    }

    public function delFromAccesorysListTest(FunctionalTester $I) {
        $I->amOnPage("/vehicles/accesories/list?menu=stock&item=accesories");
        $I->click('#delButton' . $this->id);
        $I->dontSeeInDatabase('accesories', array('id' => intval($this->id), 'deleted_at' => null));
    }

    public function delFromAccesoryFormTest(FunctionalTester $I) {
        $this->addAccesoryTest($I);
        $this->_before($I);
        $I->amOnPage("/vehicles/accesories/list?menu=stock&item=accesories");        
        $I->click('#editButton' . $this->id);
        $I->click('Eliminar');
        $I->dontSeeInDatabase('accesories', array('id' => intval($this->id), 'deleted_at' => null));
    }  
}
