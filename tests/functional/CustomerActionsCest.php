<?php

class CustomerActionsCest
{
    protected $name, $fiscalId, $id, $permitted_chars;
    
    public function _before(FunctionalTester $I)  {
        $I->amOnPage("/");
        $I->submitForm('#loginForm', ['email' => 'tonyllomouse@gmail.com', 'password' => '12345']);
    }

    // tests
    public function addCustomerTest(FunctionalTester $I) {
        $I->amOnPage("customers/list?menu=stock&item=customers");
        $I->click('#newButton');
        $this->permitted_chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $this->name = substr(str_shuffle($this->permitted_chars), 0, 20);  
        $this->fiscalId = substr(str_shuffle($this->permitted_chars), 0, 10);
        $customer = ['name' => $this->name, 'fiscalId' => $this->fiscalId];
        $I->submitForm('#formCliente', $customer);        
        $this->id = $I->grabFromDatabase('customers', 'id', ['fiscalId' => $this->fiscalId]);        
        $I->seeInDatabase('customers', ['fiscalId' => $this->fiscalId]);
    }

    public function editCustomerTest(FunctionalTester $I) {
        $I->amOnPage("customers/list?menu=stock&item=customers");
        $I->click('#editButton' . $this->id);        
        $this->fiscalId = substr(str_shuffle($this->permitted_chars), 0, 10);
        $customer = ['name' => $this->name, 'keyString' => $this->fiscalId];
        $I->submitForm('#formCliente', $customer);
        $I->see('Updated');
    }

    public function delFromCustomersListTest(FunctionalTester $I) {
        $I->amOnPage("customers/list?menu=stock&item=customers");
        $I->click('#delButton' . $this->id);
        $I->dontSeeInDatabase('customers', array('id' => intval($this->id), 'deleted_at' => null));
    }

    public function delFromCustomerFormTest(FunctionalTester $I) {
        $this->addCustomerTest($I);
        $this->_before($I);
        $I->amOnPage("customers/list?menu=stock&item=customers");        
        $I->click('#editButton' . $this->id);
        $I->click('Eliminar');
        $I->dontSeeInDatabase('customers', array('id' => intval($this->id), 'deleted_at' => null));
    }
}
