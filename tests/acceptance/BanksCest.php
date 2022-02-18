<?php
namespace Tests\acceptance;

use AcceptanceTester;
use Tests\acceptance\FirstCest;
class BanksCest
{
    protected $id;
    protected $fiscalId;
    public function _before(AcceptanceTester $I) {
        FirstCest::loginTest($I);
    }
    // tests
    public function accesBank(AcceptanceTester $I) {
        $I->wantTo('Acces Banks Pages');
        $I->amOnPage('/Intranet/');
        $I->amOnPage('/Intranet/admin');
        $I->click('Bancos', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/banks/list');
        $I->click('#submit', '#addBank');
        $I->seeCurrentUrlEquals('/Intranet/banks/form');        
    }
    public function saveBankTest(AcceptanceTester $I){
        $I->amOnPage('/Intranet/admin');
        $I->click('Bancos', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/banks/list');
        $caracteres_permitidos = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';        
        $longitud = 10;       
        $email = substr(str_shuffle($caracteres_permitidos), 0, $longitud)."@".substr(str_shuffle($caracteres_permitidos), 0, $longitud).".com";
        $this->fiscalId = substr(str_shuffle($caracteres_permitidos), 0, $longitud);        
        $I->wantTo('Create a new Bank');
        $I->click('#submit', '#addBank');
        $I->seeCurrentUrlEquals('/Intranet/banks/form'); 
        $I->submitForm('#banksForm', array ('bankCode' => 2100,
            'name' => 'Lorem',
            'fiscalId' => $this->fiscalId,
            'fiscalName' => 'LoremIpsum',
            'address' => 'Lorem ipsum, 25',
            'city' => 'Ipsum', 
            'postalCode' => 15874, 
            'state' => 'Lorem', 
            'country' => 'LoremIpsum',
            'phone' => 95874581,
            'email' => $email));
        $this->id = $I->grabFromDatabase('banks', 'id', array('fiscalId' => $this->fiscalId));
        $I->see('Saved');       
    }
    public function updateBankTest(AcceptanceTester $I){
        $I->amOnPage('/Intranet/admin');
        $I->click('Bancos', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/banks/list');
        $caracteres_permitidos = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';        
        $longitud = 10;
        $email = substr(str_shuffle($caracteres_permitidos), 0, $longitud)."@".substr(str_shuffle($caracteres_permitidos), 0, $longitud).".com";
        $I->wantTo('Update Bank');
        $I->amOnPage('/Intranet/banks/form?id='.$this->id);
        $I->submitForm('#banksForm', array ('id' => $this->id,
            'bankCode' => 2100,
            'name' => 'Lorem',
            'fiscalId' => $this->fiscalId,
            'fiscalName' => 'LoremIpsum',
            'address' => 'Lorem ipsum, 25',
            'city' => 'Ipsum', 
            'postalCode' => 15874, 
            'state' => 'Lorem', 
            'country' => 'LoremIpsum',
            'phone' => 95874581,
            'email' => $email));
        $I->see('Updated'); 
    }
    public function deleteBankTest(AcceptanceTester $I){
        $I->wantTo('Delete Bank');
        $I->amOnPage('/Intranet/admin');
        $I->click('Bancos', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/banks/list');
        $I->amOnPage('/Intranet/banks/delete?id='.$this->id); 
        $I->dontSeeInDatabase('banks', array('id' => intval($this->id), 'deleted_at' => null));
    }
}
