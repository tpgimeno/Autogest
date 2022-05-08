<?php
namespace Tests\acceptance;

use AcceptanceTester;
use Tests\acceptance\FirstCest;
class ProvidersCest
{
    public $id;
    protected $fiscalId;
    public function _before(AcceptanceTester $I) {
        FirstCest::loginTest($I);
    }
    // tests
    public function accesProvidor(AcceptanceTester $I) {
        $I->wantTo('Acces Providers Pages');
        $I->amOnPage('/Intranet/');
        $I->amOnPage('/Intranet/admin');
        $I->click('Proveedores', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/buys/providers/list');
        $I->click('#submit', '#addProvidor');
        $I->seeCurrentUrlEquals('/Intranet/buys/providers/form');        
    }
    public function saveProvidorTest(AcceptanceTester $I){
        $I->amOnPage('/Intranet/admin');
        $I->click('Proveedores', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/buys/providers/list');
        $caracteres_permitidos = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';        
        $longitud = 10;       
        $email = substr(str_shuffle($caracteres_permitidos), 0, $longitud)."@".substr(str_shuffle($caracteres_permitidos), 0, $longitud).".com";
        $this->fiscalId = substr(str_shuffle($caracteres_permitidos), 0, $longitud);        
        $I->wantTo('Create a new Providor');
        $I->click('#submit', '#addProvidor');
        $I->seeCurrentUrlEquals('/Intranet/buys/providers/form'); 
        $I->submitForm('#providersForm', array ('name' => 'Lorem',
            'fiscalId' => $this->fiscalId,
            'fiscalName' => 'LoremIpsum',
            'address' => 'Lorem ipsum, 25',
            'city' => 'Ipsum', 
            'postalCode' => 15874, 
            'state' => 'Lorem', 
            'country' => 'LoremIpsum',
            'phone' => 95874581,
            'email' => $email));
        $this->id = $I->grabFromDatabase('providers', 'id', array('fiscalId' => $this->fiscalId));
        $I->see('Saved');       
    }
    public function updateProvidorTest(AcceptanceTester $I){
        $I->amOnPage('/Intranet/admin');
        $I->click('Proveedores', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/buys/providers/list');
        $caracteres_permitidos = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';        
        $longitud = 10;
        $email = substr(str_shuffle($caracteres_permitidos), 0, $longitud)."@".substr(str_shuffle($caracteres_permitidos), 0, $longitud).".com";
        $I->wantTo('Update Providor');
        $I->amOnPage('/Intranet/buys/providers/form?id='.$this->id);
        $I->submitForm('#providersForm', array ('id' => $this->id,
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
    public function deleteProvidorTest(AcceptanceTester $I){
        $I->wantTo('Delete Providor');
        $I->amOnPage('/Intranet/admin');
        $I->click('Proveedores', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/buys/providers/list');
        $I->amOnPage('/Intranet/buys/providers/delete?id='.$this->id); 
        $I->dontSeeInDatabase('providers', array('id' => intval($this->id), 'deleted_at' => null));
    }
}
