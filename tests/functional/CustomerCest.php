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
        $fiscal_id = substr(str_shuffle($caracteres_permitidos), 0, $longitud);        
        $I->amOnPage('/intranet/admin');
        $I->click('Clientes', '.list-group-item');
        $I->seeCurrentUrlEquals('/intranet/customers/list');
        $I->wantTo('Create a new Customer');
        $I->click('#submit', '#addCustomer');
        $I->submitForm('#CustomerForm', 
                array('fiscal_id' => $fiscal_id,
                    'name' => 'Lorem Ipsum',
                    'type' => 'Particular',
                    'address' => 'C/ Lorem ipsum, 5',
                    'city' => 'Lorem ipsum',
                    'postal_code' => '12345',
                    'state' => 'Lorem',
                    'country' => 'LoremIpsum',
                    'phone' => '65451386',
                    'email' => 'loremipsum@loremipsum.com',
                    'birth' => '12/06/1995'
                    ));   
        $this->id = $I->grabFromDatabase('customers', 'id', array('fiscal_id' => $fiscal_id));
        $I->canSee('Saved');
    }
    public function UpdateCustomerTest(FunctionalTester $I)
    {
        $I->amOnPage('/intranet/admin');
        $I->click('Clientes', '.list-group-item');
        $I->seeCurrentUrlEquals('/intranet/customers/list');
        $I->wantTo('Update Customer');
        $I->amOnPage('/intranet/customers/form?id='.$this->id);
        $I->click('#submit');
        $I->canSee('Updated');     
    }
    public function DeleteCustomerTest(FunctionalTester $I)
    {
        $I->amOnPage('/intranet/admin');
        $I->click('Clientes', '.list-group-item');
        $I->seeCurrentUrlEquals('/intranet/customers/list');
        $I->wantTo('Delete Customer');
        $I->amOnPage('/intranet/customers/delete?id='.$this->id);
        $I->canSee('Clientes');
    }
}