<?php

namespace Tests\acceptance;

use AcceptanceTester;
use Tests\acceptance\FirstCest;

class BrandsCest
{
    public $id;   
    public $name;  
    public function _before(AcceptanceTester $I) {
        FirstCest::loginTest($I);
    }
    public function accesBrand(AcceptanceTester $I) {
        $I->wantTo('Acces Brand Pages');
        $I->amOnPage('/Intranet/');
        $I->amOnPage('/Intranet/admin');
        $I->click('Marcas', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/vehicles/brands/list');        
        $I->click('#submit', '#addBrand');
        $I->seeCurrentUrlEquals('/Intranet/vehicles/brands/form'); 
    }
    public function saveBrandTest(AcceptanceTester $I){
        $I->amOnPage('/Intranet/admin');
        $I->click('Marcas', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/vehicles/brands/list');
        $caracteres_permitidos = '1234567890ABCDEFGHIJKLMNÑOPQRSTUVWXYZ';        
        $longitud = 8;        
        $this->name = substr(str_shuffle($caracteres_permitidos), 0, $longitud);        
        $I->wantTo('Create a new Brand');
        $I->click('#submit', '#addBrand');
        $I->seeCurrentUrlEquals('/Intranet/vehicles/brands/form');       
        $I->submitForm('#brandsForm', array('name' => $this->name));        
        $this->id = $I->grabFromDatabase('brands', 'id', array('name' => $this->name));
        $I->see('Saved');       
    }
    public function updateBrandTest(AcceptanceTester $I){
        $I->amOnPage('/Intranet/admin');
        $I->click('Marcas', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/vehicles/brands/list');       
        $I->wantTo('Update Brand');
        $I->amOnPage('/Intranet/vehicles/brands/form?id='.$this->id);
        $caracteres_permitidos = '123456789012345678901234567890';
        $longitud = 8;        
        $this->name = substr(str_shuffle($caracteres_permitidos), 0, $longitud); 
        $I->submitForm('#brandsForm', array('id' => $this->id, 'name' => $this->name));
        $I->see('Updated'); 
    }
     public function deleteBrandTest(AcceptanceTester $I){
        $I->wantTo('Delete Brand');
        $I->amOnPage('/Intranet/admin');
        $I->click('Marcas', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/vehicles/brands/list');
        $I->amOnPage('/Intranet/vehicles/brands/delete?id='.$this->id); 
        $I->dontSeeInDatabase('brands', array('id' => intval($this->id), 'deleted_at' => null));
    }
}
