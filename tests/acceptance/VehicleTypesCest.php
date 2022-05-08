<?php

namespace Tests\acceptance;

use AcceptanceTester;
use Tests\acceptance\FirstCest;

class VehicleTypesCest
{
    public $id;   
    protected $name;  
    public function _before(AcceptanceTester $I) {
        FirstCest::loginTest($I);
    }
    public function accesVehicleType(AcceptanceTester $I) {
        $I->wantTo('Acces VehicleType Pages');
        $I->amOnPage('/Intranet/');
        $I->amOnPage('/Intranet/admin');
        $I->click('Tipos Vehículo', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/vehicles/vehicleTypes/list');        
        $I->click('#submit', '#addVehicleType');
        $I->seeCurrentUrlEquals('/Intranet/vehicles/vehicleTypes/form'); 
    }
    public function saveVehicleTypeTest(AcceptanceTester $I){
        $I->amOnPage('/Intranet/admin');
        $I->click('Tipos Vehículo', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/vehicles/vehicleTypes/list');
        $caracteres_permitidos = '1234567890ABCDEFGHIJKLMNÑOPQRSTUVWXYZ';        
        $longitud = 8;        
        $this->name = substr(str_shuffle($caracteres_permitidos), 0, $longitud);        
        $I->wantTo('Create a new VehicleType');
        $I->click('#submit', '#addVehicleType');
        $I->seeCurrentUrlEquals('/Intranet/vehicles/vehicleTypes/form');       
        $I->submitForm('#vehicleTypesForm', array('name' => $this->name));        
        $this->id = $I->grabFromDatabase('vehicletypes', 'id', array('name' => $this->name));
        $I->see('Saved');       
    }
    public function updateVehicleTypeTest(AcceptanceTester $I){
        $I->amOnPage('/Intranet/admin');
        $I->click('Tipos Vehículo', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/vehicles/vehicleTypes/list');       
        $I->wantTo('Update VehicleType');
        $I->amOnPage('/Intranet/vehicles/vehicleTypes/form?id='.$this->id);
        $caracteres_permitidos = '123456789012345678901234567890';
        $longitud = 8;        
        $this->name = substr(str_shuffle($caracteres_permitidos), 0, $longitud); 
        $I->submitForm('#vehicleTypesForm', array('id' => $this->id, 'name' => $this->name));
        $I->see('Updated'); 
    }
     public function deleteVehicleTypeTest(AcceptanceTester $I){
        $I->wantTo('Delete VehicleType');
        $I->amOnPage('/Intranet/admin');
        $I->click('Tipos Vehículo', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/vehicles/vehicleTypes/list');
        $I->amOnPage('/Intranet/vehicles/vehicleTypes/delete?id='.$this->id); 
        $I->dontSeeInDatabase('vehicletypes', array('id' => intval($this->id), 'deleted_at' => null));  
    }
}
