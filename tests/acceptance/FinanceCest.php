<?php
namespace Tests\acceptance;

use AcceptanceTester;
use Tests\acceptance\FirstCest;
class FinanceCest
{
    protected $id;
    protected $fiscalId;
    public function _before(AcceptanceTester $I) {
        FirstCest::loginTest($I);
    }
    // tests
    public function accesFinance(AcceptanceTester $I) {
        $I->wantTo('Acces Finance Pages');
        $I->amOnPage('/Intranet/');
        $I->amOnPage('/Intranet/admin');
        $I->click('Financieras', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/finance/list');
        $I->click('#submit', '#addFinance');
        $I->seeCurrentUrlEquals('/Intranet/finance/form');        
    }
    public function saveFinanceTest(AcceptanceTester $I){
        $I->amOnPage('/Intranet/admin');
        $I->click('Financieras', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/finance/list');
        $caracteres_permitidos = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';        
        $longitud = 10;       
        $email = substr(str_shuffle($caracteres_permitidos), 0, $longitud)."@".substr(str_shuffle($caracteres_permitidos), 0, $longitud).".com";
        $this->fiscalId = substr(str_shuffle($caracteres_permitidos), 0, $longitud);        
        $I->wantTo('Create a new Finance');
        $I->click('#submit', '#addFinance');
        $I->seeCurrentUrlEquals('/Intranet/finance/form'); 
        $I->submitForm('#financeForm', array ('name' => 'Lorem',
            'fiscalId' => $this->fiscalId,
            'fiscalName' => 'LoremIpsum',
            'address' => 'Lorem ipsum, 25',
            'city' => 'Ipsum', 
            'postalCode' => 15874, 
            'state' => 'Lorem', 
            'country' => 'LoremIpsum',
            'phone' => 95874581,
            'email' => $email));
        $this->id = $I->grabFromDatabase('finance', 'id', array('fiscalId' => $this->fiscalId));
        $I->see('Saved');       
    }
    public function updateFinanceTest(AcceptanceTester $I){
        $I->amOnPage('/Intranet/admin');
        $I->click('Financieras', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/finance/list');
        $caracteres_permitidos = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';        
        $longitud = 10;
        $email = substr(str_shuffle($caracteres_permitidos), 0, $longitud)."@".substr(str_shuffle($caracteres_permitidos), 0, $longitud).".com";
        $I->wantTo('Update Finance');
        $I->amOnPage('/Intranet/finance/form?id='.$this->id);
        $I->submitForm('#financeForm', array ('id' => $this->id, 'name' => 'Lorem',
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
     public function deleteFinanceTest(AcceptanceTester $I){
        $I->wantTo('Delete Finance');
        $I->amOnPage('/Intranet/admin');
        $I->click('Financieras', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/finance/list');
        $I->amOnPage('/Intranet/finance/delete?id='.$this->id); 
        $I->dontSeeInDatabase('finance', array('id' => intval($this->id), 'deleted_at' => null));
    }
}
