<?php
namespace Tests\acceptance;

use AcceptanceTester;
use Tests\acceptance\FirstCest;
class StoresCest
{
    protected $id;
    protected $name;
    public function _before(AcceptanceTester $I) {
        FirstCest::loginTest($I);
    }
    // tests
    public function accesStore(AcceptanceTester $I) {
        $I->wantTo('Acces Store Pages');
        $I->amOnPage('/Intranet/');
        $I->amOnPage('/Intranet/admin');
        $I->click('Almacenes', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/stores/list');        
        $I->click('#submit', '#addStore');
        $I->seeCurrentUrlEquals('/Intranet/stores/form'); 
    }
    public function saveStoreTest(AcceptanceTester $I){
        $I->amOnPage('/Intranet/admin');
        $I->click('Almacenes', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/stores/list');
        $caracteres_permitidos = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';        
        $longitud = 10;
        $long_name = 6;
        $email = substr(str_shuffle($caracteres_permitidos), 0, $longitud)."@".substr(str_shuffle($caracteres_permitidos), 0, $longitud).".com";
        $this->name = substr(str_shuffle($caracteres_permitidos), 0, $long_name);
        $I->wantTo('Create a new Store');
        $I->click('#submit', '#addStore');
        $I->seeCurrentUrlEquals('/Intranet/stores/form'); 
        $I->submitForm('#storesForm', array ('name' => $this->name,
            'address' => 'Lorem ipsum, 25',
            'city' => 'Ipsum', 
            'postal_code' => 15874, 
            'state' => 'Lorem', 
            'country' => 'LoremIpsum',
            'phone' => 95874581,
            'email' => $email));
        $this->id = $I->grabFromDatabase('stores', 'id', array('name' => 'Lorem'));
        $I->see('Saved');       
    }
    public function updateStoreTest(AcceptanceTester $I){
        $I->amOnPage('/Intranet/admin');
        $I->click('Almacenes', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/stores/list');
        $caracteres_permitidos = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';        
        $longitud = 10;
        $email = substr(str_shuffle($caracteres_permitidos), 0, $longitud)."@".substr(str_shuffle($caracteres_permitidos), 0, $longitud).".com";
        $I->wantTo('Update Store');
        $I->amOnPage('/Intranet/stores/form?id='.$this->id);
        $I->submitForm('#storesForm', array ('id' => $this->id, 'name' => $this->name,
            'address' => 'Lorem ipsum, 25',
            'city' => 'Ipsum', 
            'postal_code' => 15874, 
            'state' => 'Lorem', 
            'country' => 'LoremIpsum',
            'phone' => 95874581,
            'email' => $email));
        $I->see('Updated'); 
    }
     public function deleteStoreTest(AcceptanceTester $I){
        $I->wantTo('Delete Store');
        $I->amOnPage('/Intranet/admin');
        $I->click('Almacenes', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/stores/list');
        $I->amOnPage('/Intranet/stores/delete?id='.$this->id); 
        $I->dontSeeInDatabase('stores', array('id' => intval($this->id), 'deleted_at' => null));
    }
}
