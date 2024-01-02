<?php

class BrandsActionsCest
{
    protected $name, $id, $permitted_chars;
    
    public function _before(FunctionalTester $I) {
        $I->amOnPage("/");
        $I->submitForm('#loginForm', ['email' => 'tonyllomouse@gmail.com', 'password' => '12345']);
    }

    // tests
    public function addBrandTest(FunctionalTester $I) {
        $I->amOnPage("/vehicles/brands/list?menu=stock&item=brands");
        $I->click('#newButton');
        $this->permitted_chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $this->name = substr(str_shuffle($this->permitted_chars), 0, 12);        
        $brand = ['name' => $this->name];
        $I->submitForm('#formMarca', $brand);        
        $this->id = $I->grabFromDatabase('brands', 'id', ['name' => $this->name]);        
        $I->seeInDatabase('brands', ['name' => $this->name]);
    }

    public function editBrandTest(FunctionalTester $I) {
        $I->amOnPage("/vehicles/brands/list?menu=stock&item=brands");
        $I->click('#editButton' . $this->id);        
        $key = substr(str_shuffle($this->permitted_chars), 0, 20);
        $brand = ['name' => $this->name, 'keyString' => $key];
        $I->submitForm('#formMarca', $brand);
        $I->see('Updated');
    }

    public function delFromBrandsListTest(FunctionalTester $I) {
        $I->amOnPage("/vehicles/brands/list?menu=stock&item=brands");
        $I->click('#delButton' . $this->id);
        $I->dontSeeInDatabase('brands', array('id' => intval($this->id), 'deleted_at' => null));
    }

    public function delFromBrandFormTest(FunctionalTester $I) {
        $this->addBrandTest($I);
        $this->_before($I);
        $I->amOnPage("/vehicles/brands/list?menu=stock&item=brands");        
        $I->click('#editButton' . $this->id);
        $I->click('Eliminar');
        $I->dontSeeInDatabase('brands', array('id' => intval($this->id), 'deleted_at' => null));
    } 
}
