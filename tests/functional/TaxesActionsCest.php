<?php

class TaxesActionsCest
{
    protected $name, $id, $permitted_chars;
    
    public function _before(FunctionalTester $I) {
        $I->amOnPage("/");
        $I->submitForm('#loginForm', ['email' => 'tonyllomouse@gmail.com', 'password' => '12345']);
    }

    // tests
    public function addTaxTest(FunctionalTester $I) {
        $I->amOnPage("/taxes/list?menu=stock&item=taxes");
        $I->click('#newButton');
        $this->permitted_chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $this->name = substr(str_shuffle($this->permitted_chars), 0, 12);    
        $percentaje = substr(str_shuffle('0123456789'), 0, 2);
        $tax = ['name' => $this->name, 'percentaje' => $percentaje];
        $I->submitForm('#formTipodeIva', $tax);        
        $this->id = $I->grabFromDatabase('taxes', 'id', ['name' => $this->name]);        
        $I->seeInDatabase('taxes', ['name' => $this->name]);
    }

    public function editTaxTest(FunctionalTester $I) {
        $I->amOnPage("/taxes/list?menu=stock&item=taxes");
        $I->click('#editButton' . $this->id);        
        $percentaje = substr(str_shuffle('0123456789'), 0, 2);
        $tax = ['name' => $this->name, 'percentaje' => $percentaje];
        $I->submitForm('#formTipodeIva', $tax);    
        $I->submitForm('#formTipodeIva', $tax);
        $I->see('Updated');
    }

    public function delFromTaxsListTest(FunctionalTester $I) {
        $I->amOnPage("/taxes/list?menu=stock&item=taxes");
        $I->click('#delButton' . $this->id);
        $I->dontSeeInDatabase('taxes', array('id' => intval($this->id), 'deleted_at' => null));
    }

    public function delFromTaxFormTest(FunctionalTester $I) {
        $this->addTaxTest($I);
        $this->_before($I);
        $I->amOnPage("/taxes/list?menu=stock&item=taxes");        
        $I->click('#editButton' . $this->id);
        $I->click('Eliminar');
        $I->dontSeeInDatabase('taxes', array('id' => intval($this->id), 'deleted_at' => null));
    }
}
