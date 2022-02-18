<?php
namespace Tests\acceptance;

use AcceptanceTester;
use Tests\acceptance\FirstCest;
class AccountsCest
{
    protected $id;   
    protected $iban;
    protected $bank;
    protected $owner;
    public function _before(AcceptanceTester $I) {
        FirstCest::loginTest($I);
    }
    public function accesAccount(AcceptanceTester $I) {
        $I->wantTo('Acces Account Pages');
        $I->amOnPage('/Intranet/');
        $I->amOnPage('/Intranet/admin');
        $I->click('Cuentas Bancarias', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/accounts/list');        
        $I->click('#submit', '#addAccount');
        $I->seeCurrentUrlEquals('/Intranet/accounts/form'); 
    }
    public function saveAccountTest(AcceptanceTester $I){
        $I->amOnPage('/Intranet/admin');
        $I->click('Cuentas Bancarias', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/accounts/list');
        $caracteres_permitidos = '123456789012345678901234567890';        
        $longitud = 18;        
        $this->iban = "ES".substr(str_shuffle($caracteres_permitidos), 0, $longitud);   
        $this->bank = $I->grabFromDatabase('banks', 'id', array('name' => 'Caixabank'));
        $this->owner = $I->grabFromDatabase('company', 'id', array('name' => 'AUTOMOTIVE SERVICES 2014 SLU'));
        $I->wantTo('Create a new Account');
        $I->click('#submit', '#addAccount');
        $I->seeCurrentUrlEquals('/Intranet/accounts/form'); 
        $I->submitForm('#accountsForm', array ('bank' => $this->bank,
            'owner' => $this->owner,
            'accountNumber' => $this->iban, 
            'observations' => 'Lorem Ipsum'));
        $this->id = $I->grabFromDatabase('accounts', 'id', array('accountNumber' => $this->iban));
        $I->see('Saved');       
    }
    public function updateAccountTest(AcceptanceTester $I){
        $I->amOnPage('/Intranet/admin');
        $I->click('Cuentas Bancarias', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/accounts/list');       
        $I->wantTo('Update Account');
        $I->amOnPage('/Intranet/accounts/form?id='.$this->id);
        $I->submitForm('#accountsForm', array ('id' => $this->id, 'bank' => $this->bank,
            'owner' => $this->owner,
            'accountNumber' => $this->iban, 
            'observations' => 'Prueba Actualización'));
        $I->see('Updated'); 
    }
     public function deleteAccountTest(AcceptanceTester $I){
        $I->wantTo('Delete Account');
        $I->amOnPage('/Intranet/admin');
        $I->click('Cuentas Bancarias', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/accounts/list');
        $I->amOnPage('/Intranet/accounts/delete?id='.$this->id); 
        $I->dontSeeInDatabase('accounts', array('id' => intval($this->id), 'deleted_at' => null));
    }
}
