<?php
namespace Tests\acceptance;

use AcceptanceTester;
use Tests\acceptance\FirstCest;
class CustomersCest
{
    protected $id;
    protected $fiscalId;   
    public function _before(AcceptanceTester $I) {
        FirstCest::loginTest($I);
    }
    // tests
    public function accesCustomer(AcceptanceTester $I) {
        $I->wantTo('Acces Customers Pages');
        $I->amOnPage('/Intranet/');
        $I->amOnPage('/Intranet/admin');
        $I->click('Clientes', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/customers/list');
        $I->click('#submit', '#addCustomer');
        $I->seeCurrentUrlEquals('/Intranet/customers/form');        
    }
    public function saveCustomerTest(AcceptanceTester $I){
        $I->amOnPage('/Intranet/admin');
        $I->click('Clientes', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/customers/list');
        $caracteres_permitidos = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';        
        $longitud = 10;         
        $email = substr(str_shuffle($caracteres_permitidos), 0, $longitud)."@".substr(str_shuffle($caracteres_permitidos), 0, $longitud).".com";
        $this->fiscalId = substr(str_shuffle($caracteres_permitidos), 0, $longitud);        
        $I->wantTo('Create a new Customer');
        $I->click('#submit', '#addCustomer');
        $I->seeCurrentUrlEquals('/Intranet/customers/form'); 
        $I->submitForm('#customersForm', array ('name' => 'Lorem', 
            'fiscalId' => $this->fiscalId, 
            'customerType' => 'PARTICULAR',
            'address' => 'Lorem ipsum, 25',
            'city' => 'Ipsum', 
            'postalCode' => 15874, 
            'state' => 'Lorem', 
            'country' => 'LoremIpsum',
            'phone' => 95874581,
            'email' => $email,
            'birthDate' => '12/10/1978'));
        $this->id = $I->grabFromDatabase('customers', 'id', array('fiscalId' => $this->fiscalId));
        $I->see('Saved');       
    }
    public function updateCustomerTest(AcceptanceTester $I){
        $I->amOnPage('/Intranet/admin');
        $I->click('Clientes', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/customers/list');
        $caracteres_permitidos = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';        
        $longitud = 10;
        $email = substr(str_shuffle($caracteres_permitidos), 0, $longitud)."@".substr(str_shuffle($caracteres_permitidos), 0, $longitud).".com";
        $I->wantTo('Update Customer');
        $I->amOnPage('/Intranet/customers/form?id='.$this->id);
        $I->submitForm('#customersForm', array ('id' => $this->id,
            'name' => 'Lorem', 
            'customerType' => 'PARTICULAR',
            'fiscalId' => $this->fiscalId,                      
            'address' => 'Lorem ipsum, 25',
            'city' => 'Ipsum', 
            'postalCode' => 15874, 
            'state' => 'Lorem', 
            'country' => 'LoremIpsum',
            'phone' => 95874581,
            'email' => $email,
            'birthDate' => '12/10/1978'));
        $I->see('Updated'); 
    }
    public function deleteCustomerTest(AcceptanceTester $I){
        $I->wantTo('Delete Customer');
        $I->amOnPage('/Intranet/admin');
        $I->click('Clientes', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/customers/list');
        $I->amOnPage('/Intranet/customers/delete?id='.$this->id); 
        $I->dontSeeInDatabase('customers', array('id' => intval($this->id), 'deleted_at' => null));
    }
}
