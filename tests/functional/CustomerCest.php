<?php 

class CustomerCest
{
    protected $id;
    
    public function _before(FunctionalTester $I)
    {
        homeCest::loginTest($I);
    }

    // tests
    public function SaveCustomerTest(FunctionalTester $I)
    {
        $caracteres_permitidos = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $longitud = 7;        
        $fiscalId = substr(str_shuffle($caracteres_permitidos), 0, $longitud);        
        $I->amOnPage('/Intranet/admin');
        $I->click('Clientes', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/customers/list');
        $I->wantTo('Create a new Customer');
        $I->click('#submit', '#addCustomer');
        $I->seeCurrentUrlEquals('/Intranet/customers/form');
        $I->submitForm('#CustomerForm', array('fiscalId' => $fiscalId, 'name' => 'Lorem Ipsum', 'type' => 'Particular', 'address' => 'C/ Lorem ipsum, 5', 'city' => 'Lorem ipsum', 'postalCode' => '12345', 'state' => 'Lorem', 'country' => 'LoremIpsum', 'phone' => '65451386', 'email' => 'loremipsum@loremipsum.com', 'birth' => '12/06/1995'));   
        $this->id = $I->grabFromDatabase('customers', 'id', array('fiscalId' => $fiscalId));
       
    }
    public function UpdateCustomerTest(FunctionalTester $I)
    {
        $I->amOnPage('/Intranet/admin');
        $I->click('Clientes', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/customers/list');
        $I->wantTo('Update Customer');
        $I->amOnPage('/Intranet/customers/form?id='.$this->id);       
          
    }
    public function DeleteCustomerTest(FunctionalTester $I)
    {
        $I->amOnPage('/Intranet/admin');
        $I->click('Clientes', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/customers/list');
        $I->wantTo('Delete Customer');
        $I->amOnPage('/Intranet/customers/delete?id='.$this->id);
       
    }
}
