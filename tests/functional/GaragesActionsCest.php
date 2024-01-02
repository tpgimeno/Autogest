<?php

class GaragesActionsCest
{
    protected $name,$fiscalId, $id, $permitted_chars;
    
    public function _before(FunctionalTester $I)  {
        $I->amOnPage("/");
        $I->submitForm('#loginForm', ['email' => 'tonyllomouse@gmail.com', 'password' => '12345']);
    }

    // tests
    public function addGarageTest(FunctionalTester $I) {
        $I->amOnPage("/garages/list?menu=stock&item=garages");
        $I->click('#newButton');
        $this->permitted_chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $this->name = substr(str_shuffle($this->permitted_chars), 0, 12);  
        $this->fiscalId = substr(str_shuffle($this->permitted_chars), 0, 20);  
        $garage = ['name' => $this->name, 'fiscalId' => $this->fiscalId, 'email' => 'lorem@lorem.com', 'phone' => '675544333'];
        $I->submitForm('#formTaller', $garage);        
        $this->id = $I->grabFromDatabase('garages', 'id', ['name' => $this->name]);        
        $I->seeInDatabase('garages', ['name' => $this->name]);
    }

    public function editGarageTest(FunctionalTester $I) {
        $I->amOnPage("/garages/list?menu=stock&item=garages");
        $I->click('#editButton' . $this->id);        
        $this->fiscalId = substr(str_shuffle($this->permitted_chars), 0, 20);  
        $garage = ['fiscalId' => $this->fiscalId];
        $I->submitForm('#formTaller', $garage);
        $I->see('Updated');
    }

    public function delFromGaragesListTest(FunctionalTester $I) {
        $I->amOnPage("/garages/list?menu=stock&item=garages");
        $I->click('#delButton' . $this->id);
        $I->dontSeeInDatabase('garages', array('id' => intval($this->id), 'deleted_at' => null));
    }

    public function delFromGarageFormTest(FunctionalTester $I) {
        $this->addGarageTest($I);
        $this->_before($I);
        $I->amOnPage("/garages/list?menu=stock&item=garages");        
        $I->click('#editButton' . $this->id);
        $I->click('Eliminar');
        $I->dontSeeInDatabase('garages', array('id' => intval($this->id), 'deleted_at' => null));
    } 
}
