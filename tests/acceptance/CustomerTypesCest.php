<?php

namespace Tests\acceptance;

use AcceptanceTester;
use Tests\acceptance\FirstCest;

class CustomerTypesCest
{
    protected $id;   
    protected $name;  
    public function _before(AcceptanceTester $I) {
        FirstCest::loginTest($I);
    }
    public function accesCustomerType(AcceptanceTester $I) {
        $I->wantTo('Acces CustomerType Pages');
        $I->amOnPage('/Intranet/');
        $I->amOnPage('/Intranet/admin');
        $I->click('Tipos Clientes', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/customers/type/list');        
        $I->click('#submit', '#addCustomerType');
        $I->seeCurrentUrlEquals('/Intranet/customers/type/form'); 
    }
    public function saveCustomerTypeTest(AcceptanceTester $I){
        $I->amOnPage('/Intranet/admin');
        $I->click('Tipos Clientes', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/customers/type/list');
        $caracteres_permitidos = '1234567890ABCDEFGHIJKLMNÑOPQRSTUVWXYZ';        
        $longitud = 8;        
        $this->name = substr(str_shuffle($caracteres_permitidos), 0, $longitud);        
        $I->wantTo('Create a new CustomerType');
        $I->click('#submit', '#addCustomerType');
        $I->seeCurrentUrlEquals('/Intranet/customers/type/form');       
        $I->submitForm('#customerTypesForm', array('name' => $this->name));        
        $this->id = $I->grabFromDatabase('customertypes', 'id', array('name' => $this->name));
        $I->see('Saved');       
    }
    public function updateCustomerTypeTest(AcceptanceTester $I){
        $I->amOnPage('/Intranet/admin');
        $I->click('Tipos Clientes', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/customers/type/list');       
        $I->wantTo('Update CustomerType');
        $I->amOnPage('/Intranet/customers/type/form?id='.$this->id);
        $caracteres_permitidos = '123456789012345678901234567890';
        $longitud = 8;        
        $this->name = substr(str_shuffle($caracteres_permitidos), 0, $longitud); 
        $I->submitForm('#customerTypesForm', array('id' => $this->id, 'name' => $this->name));
        $I->see('Updated'); 
    }
     public function deleteCustomerTypeTest(AcceptanceTester $I){
        $I->wantTo('Delete CustomerType');
        $I->amOnPage('/Intranet/admin');
        $I->click('Tipos Clientes', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/customers/type/list');
        $I->amOnPage('/Intranet/customers/type/delete?id='.$this->id); 
        $I->dontSeeInDatabase('customerTypes', array('id' => intval($this->id)));
    }
}
