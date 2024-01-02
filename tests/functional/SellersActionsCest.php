<?php

class SellersActionsCest
{
    protected $name,$fiscalId, $id, $permitted_chars;

    public function _before(FunctionalTester $I) {
        $I->amOnPage("/");
        $I->submitForm('#loginForm', ['email' => 'tonyllomouse@gmail.com', 'password' => '12345']);
    }

    // tests
    public function addSellerTest(FunctionalTester $I) {
        $I->amOnPage("/sellers/list?menu=stock&item=sellers");
        $I->click('#newButton');
        $this->permitted_chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $this->name = substr(str_shuffle($this->permitted_chars), 0, 12);
        $this->fiscalId = substr(str_shuffle($this->permitted_chars), 0, 10);        
        $seller = ['name' => $this->name, 'fiscalId' => $this->fiscalId];
        $I->submitForm('#formComercial', $seller);        
        $this->id = $I->grabFromDatabase('sellers', 'id', ['name' => $this->name]);        
        $I->seeInDatabase('sellers', ['name' => $this->name]);
    }

    public function editSellerTest(FunctionalTester $I) {
        $I->amOnPage("/sellers/list?menu=stock&item=sellers");
        $I->click('#editButton' . $this->id);        
        $this->fiscalId = substr(str_shuffle($this->permitted_chars), 0, 10);        
        $seller = ['name' => $this->name, 'fiscalId' => $this->fiscalId];
        $I->submitForm('#formComercial', $seller);
        $I->see('Updated');
    }

    public function delFromSellersListTest(FunctionalTester $I) {
        $I->amOnPage("/sellers/list?menu=stock&item=sellers");
        $I->click('#delButton' . $this->id);
        $I->dontSeeInDatabase('sellers', array('id' => intval($this->id), 'deleted_at' => null));
    }

    public function delFromSellerFormTest(FunctionalTester $I) {
        $this->addSellerTest($I);
        $this->_before($I);
        $I->amOnPage("/sellers/list?menu=stock&item=sellers");        
        $I->click('#editButton' . $this->id);
        $I->click('Eliminar');
        $I->dontSeeInDatabase('sellers', array('id' => intval($this->id), 'deleted_at' => null));
    } 
}
