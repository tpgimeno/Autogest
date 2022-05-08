<?php

namespace Tests\acceptance;

use AcceptanceTester;
use Tests\acceptance\FirstCest;

class ModelsCest
{
    public $id;   
    protected $name;  
    protected $brand;
    public function _before(AcceptanceTester $I) {
        FirstCest::loginTest($I);
    }
    public function accesModel(AcceptanceTester $I) {
        $I->wantTo('Acces Model Pages');
        $I->amOnPage('/Intranet/');
        $I->amOnPage('/Intranet/admin');
        $I->click('Modelos', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/vehicles/models/list');        
        $I->click('#submit', '#addModel');
        $I->seeCurrentUrlEquals('/Intranet/vehicles/models/form'); 
    }
    public function saveModelTest(AcceptanceTester $I){
        $I->amOnPage('/Intranet/admin');
        $I->click('Modelos', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/vehicles/models/list');
        $caracteres_permitidos = '1234567890ABCDEFGHIJKLMNÑOPQRSTUVWXYZ';        
        $longitud = 8;        
        $this->name = substr(str_shuffle($caracteres_permitidos), 0, $longitud);        
        $I->wantTo('Create a new Model');
        $I->click('#submit', '#addModel');
        $I->seeCurrentUrlEquals('/Intranet/vehicles/models/form'); 
        $this->brand = $I->grabFromDatabase('brands', 'name', array('id' => 1));
        $I->submitForm('#modelsForm', array('name' => $this->name, 'brand' => $this->brand));        
        $this->id = $I->grabFromDatabase('models', 'id', array('name' => $this->name));
        $I->see('Saved');       
    }
    public function updateModelTest(AcceptanceTester $I){
        $I->amOnPage('/Intranet/admin');
        $I->click('Modelos', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/vehicles/models/list');       
        $I->wantTo('Update Model');
        $I->amOnPage('/Intranet/vehicles/models/form?id='.$this->id);
        $caracteres_permitidos = '123456789012345678901234567890';
        $longitud = 8;        
        $this->name = substr(str_shuffle($caracteres_permitidos), 0, $longitud); 
        $I->submitForm('#modelsForm', array('id' => $this->id, 'name' => $this->name, 'brand' => $this->brand));
        $I->see('Updated'); 
    }
     public function deleteModelTest(AcceptanceTester $I){
        $I->wantTo('Delete Model');
        $I->amOnPage('/Intranet/admin');
        $I->click('Modelos', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/vehicles/models/list');
        $I->amOnPage('/Intranet/vehicles/models/delete?id='.$this->id); 
        $I->dontSeeInDatabase('models', array('id' => intval($this->id), 'deleted_at' => null));
    }
}
