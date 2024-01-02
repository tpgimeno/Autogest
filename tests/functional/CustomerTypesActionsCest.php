<?php

class CustomerTypesActionsCest
{
    protected $name, $id, $permitted_chars;
    
    public function _before(FunctionalTester $I) {
        $I->amOnPage("/");
        $I->submitForm('#loginForm', ['email' => 'tonyllomouse@gmail.com', 'password' => '12345']);
    }

    // tests
    public function addCustomerTypeTest(FunctionalTester $I) {
        $I->amOnPage("/customers/type/list?menu=stock&item=customerTypes");
        $I->click('#newButton');
        $this->permitted_chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $this->name = substr(str_shuffle($this->permitted_chars), 0, 12);        
        $customerType = ['name' => $this->name];
        $I->submitForm('#formTipodeCliente', $customerType);        
        $this->id = $I->grabFromDatabase('customerTypes', 'id', ['name' => $this->name]);        
        $I->seeInDatabase('customerTypes', ['name' => $this->name]);
    }

    public function editCustomerTypeTest(FunctionalTester $I) {
        $I->amOnPage("/customers/type/list?menu=stock&item=customerTypes");
        $I->click('#editButton' . $this->id);        
        $this->name = substr(str_shuffle($this->permitted_chars), 0, 12); 
        $customerType = ['name' => $this->name, 'keyString' => $this->name];
        $I->submitForm('#formTipodeCliente', $customerType);
        $I->see('Updated');
    }

    public function delFromCustomerTypesListTest(FunctionalTester $I) {
        $I->amOnPage("/customers/type/list?menu=stock&item=customerTypes");
        $I->click('#delButton' . $this->id);
        $I->dontSeeInDatabase('customerTypes', array('id' => intval($this->id), 'deleted_at' => null));
    }

    public function delFromCustomerTypeFormTest(FunctionalTester $I) {
        $this->addCustomerTypeTest($I);
        $this->_before($I);
        $I->amOnPage("/customers/type/list?menu=stock&item=customerTypes");        
        $I->click('#editButton' . $this->id);
        $I->click('Eliminar');
        $I->dontSeeInDatabase('customerTypes', array('id' => intval($this->id), 'deleted_at' => null));
    }
}
