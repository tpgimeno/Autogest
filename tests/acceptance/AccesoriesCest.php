<?php

namespace Tests\acceptance;

use AcceptanceTester;
use Tests\acceptance\FirstCest;

class AccesoriesCest
{
    protected $id;   
    protected $name;  
    public function _before(AcceptanceTester $I) {
        FirstCest::loginTest($I);
    }
    public function accesAccesory(AcceptanceTester $I) {
        $I->wantTo('Acces Accesory Pages');
        $I->amOnPage('/Intranet/');
        $I->amOnPage('/Intranet/admin');
        $I->click('Accesorios', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/vehicles/accesories/list');        
        $I->click('#submit', '#addAccesory');
        $I->seeCurrentUrlEquals('/Intranet/vehicles/accesories/form'); 
    }
    public function saveAccesoryTest(AcceptanceTester $I){
        $I->amOnPage('/Intranet/admin');
        $I->click('Accesorios', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/vehicles/accesories/list');
        $caracteres_permitidos = '1234567890ABCDEFGHIJKLMNÑOPQRSTUVWXYZ';        
        $longitud = 8;        
        $this->name = substr(str_shuffle($caracteres_permitidos), 0, $longitud);        
        $I->wantTo('Create a new Accesory');
        $I->click('#submit', '#addAccesory');
        $I->seeCurrentUrlEquals('/Intranet/vehicles/accesories/form');       
        $I->submitForm('#accesoriesForm', array('name' => $this->name));        
        $this->id = $I->grabFromDatabase('accesories', 'id', array('name' => $this->name));
        $I->see('Saved');       
    }
    public function updateAccesoryTest(AcceptanceTester $I){
        $I->amOnPage('/Intranet/admin');
        $I->click('Accesorios', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/vehicles/accesories/list');       
        $I->wantTo('Update Accesory');
        $I->amOnPage('/Intranet/vehicles/accesories/form?id='.$this->id);
        $caracteres_permitidos = '123456789012345678901234567890';
        $longitud = 8;        
        $this->name = substr(str_shuffle($caracteres_permitidos), 0, $longitud); 
        $I->submitForm('#accesoriesForm', array('id' => $this->id, 'name' => $this->name));
        $I->see('Updated'); 
    }
     public function deleteAccesoryTest(AcceptanceTester $I){
        $I->wantTo('Delete Accesory');
        $I->amOnPage('/Intranet/admin');
        $I->click('Accesorios', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/vehicles/accesories/list');
        $I->amOnPage('/Intranet/vehicles/accesories/delete?id='.$this->id); 
        $I->dontSeeInDatabase('accesories', array('id' => intval($this->id), 'deleted_at' => null));
    }
}
