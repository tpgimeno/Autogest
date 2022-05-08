<?php

namespace Tests\acceptance;

use AcceptanceTester;
use Tests\acceptance\FirstCest;

class LocationsCest
{
    public $id;   
    protected $name;
    protected $storeId;
    protected $store;
    public function _before(AcceptanceTester $I) {
        FirstCest::loginTest($I);
    }
    public function accesLocation(AcceptanceTester $I) {
        $I->wantTo('Acces Location Pages');
        $I->amOnPage('/Intranet/');
        $I->amOnPage('/Intranet/admin');
        $I->click('Ubicaciones', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/locations/list');        
        $I->click('#submit', '#addLocation');
        $I->seeCurrentUrlEquals('/Intranet/locations/form'); 
    }
    public function saveLocationTest(AcceptanceTester $I){
        $I->amOnPage('/Intranet/admin');
        $I->click('Ubicaciones', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/locations/list');
        $caracteres_permitidos = '1234567890ABCDEFGHIJKLMNÑOPQRSTUVWXYZ';        
        $longitud = 2;        
        $this->name = substr(str_shuffle($caracteres_permitidos), 0, $longitud);        
        $I->wantTo('Create a new Location');
        $I->click('#submit', '#addLocation');
        $I->seeCurrentUrlEquals('/Intranet/locations/form'); 
        $this->store = $I->grabFromDatabase('stores', 'name', array('id' => 1));
        $I->submitForm('#locationForm', array('name' => $this->name, 'store' => $this->store)); 
        $this->storeId = $I->grabFromDatabase('stores', 'id', array('name' => $this->store));
        $this->id = $I->grabFromDatabase('locations', 'id', array('name' => $this->name, 'storeId' => $this->storeId));
        $I->see('Saved');       
    }
    public function updateLocationTest(AcceptanceTester $I){
        $I->amOnPage('/Intranet/admin');
        $I->click('Ubicaciones', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/locations/list');       
        $I->wantTo('Update Location');
        $I->amOnPage('/Intranet/locations/form?id='.$this->id);
        $caracteres_permitidos = '123456789012345678901234567890';
        $longitud = 2;        
        $this->name = substr(str_shuffle($caracteres_permitidos), 0, $longitud); 
        $I->submitForm('#locationForm', array('id' => $this->id, 'name' => $this->name, 'store' => $this->store));
        $I->see('Updated'); 
    }
     public function deleteLocationTest(AcceptanceTester $I){
        $I->wantTo('Delete Location');
        $I->amOnPage('/Intranet/admin');
        $I->click('Ubicaciones', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/locations/list');
        $I->amOnPage('/Intranet/locations/delete?id='.$this->id); 
        $I->dontSeeInDatabase('locations', array('id' => intval($this->id), 'deleted_at' => null));
    }
}
