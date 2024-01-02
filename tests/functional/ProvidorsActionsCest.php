<?php

class ProvidorsActionsCest
{
    protected $name,$fiscalId, $id, $permitted_chars;
     
    public function _before(FunctionalTester $I) {
        $I->amOnPage("/");
        $I->submitForm('#loginForm', ['email' => 'tonyllomouse@gmail.com', 'password' => '12345']);
    }

    // tests
    public function addProvidorTest(FunctionalTester $I) {
        $I->amOnPage("/buys/providors/list?menu=stock&item=providors");
        $I->click('#newButton');
        $this->permitted_chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $this->name = substr(str_shuffle($this->permitted_chars), 0, 12);
        $this->fiscalId = substr(str_shuffle($this->permitted_chars), 0, 10);          
        $providor = ['name' => $this->name, 'fiscalId' => $this->fiscalId];
        $I->submitForm('#formProveedor', $providor);        
        $this->id = $I->grabFromDatabase('providors', 'id', ['name' => $this->name]);        
        $I->seeInDatabase('providors', ['name' => $this->name]);
    }

    public function editProvidorTest(FunctionalTester $I) {
        $I->amOnPage("/buys/providors/list?menu=stock&item=providors");
        $I->click('#editButton' . $this->id);        
        $this->fiscalId = substr(str_shuffle($this->permitted_chars), 0, 10);          
        $providor = ['name' => $this->name, 'fiscalId' => $this->fiscalId];
        $I->submitForm('#formProveedor', $providor);
        $I->see('Updated');
    }

    public function delFromProvidorsListTest(FunctionalTester $I) {
        $I->amOnPage("/buys/providors/list?menu=stock&item=providors");
        $I->click('#delButton' . $this->id);
        $I->dontSeeInDatabase('providors', array('id' => intval($this->id), 'deleted_at' => null));
    }

    public function delFromProvidorFormTest(FunctionalTester $I) {
        $this->addProvidorTest($I);
        $this->_before($I);
        $I->amOnPage("/buys/providors/list?menu=stock&item=providors");        
        $I->click('#editButton' . $this->id);
        $I->click('Eliminar');
        $I->dontSeeInDatabase('providors', array('id' => intval($this->id), 'deleted_at' => null));
    } 
}
