<?php
namespace Tests\acceptance;

use AcceptanceTester;
use Tests\acceptance\FirstCest;

class VehiclesCest
{
    protected $id;
    public function _before(AcceptanceTester $I) {
        FirstCest::loginTest($I);
    }

    // tests
    public function accessVehicle(AcceptanceTester $I)
    {
        $I->wantTo('Acces Vehicles Pages');
        $I->amOnPage('/Intranet/');
        $I->amOnPage('/Intranet/admin');
        $I->click('Vehículos', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/vehicles/list');
        $I->click('#submit', '#addVehicle');
        $I->seeCurrentUrlEquals('/Intranet/vehicles/form'); 
    }
    public function createVehicle(AcceptanceTester $I){
        $I->amOnPage('/Intranet/admin');
        $I->click('Vehículos', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/vehicles/list');
        $caracteres_permitidos = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';        
        $longitud = 10;         
        $matricula = substr(str_shuffle($caracteres_permitidos), 0, $longitud);
        $vin = substr(str_shuffle($caracteres_permitidos), 0, $longitud);
        $store = $I->grabFromDatabase('stores', 'name', array('id' => 1));
        $I->wantTo('Create a new Vehicle');
        $I->click('#submit', '#addVehicle');
        $I->seeCurrentUrlEquals('/Intranet/customers/form'); 
        $I->submitForm('#vehiclesForm', array ('plate' => $matricula, 
            'vin' => $vin, 
            'brand' => 'CITROEN',
            'model' => 'BERLINGO',
            'description' => 'Berlingo 1.6HDI 90cv', 
            'type' => 'FURGON', 
            'store' => 'AUTOMOTIVE', 
            'location' => 'LoremIpsum',
            'km' => 73524,
            'power' => 90,
            'places' => 2,
            'doors' => 4,
            'providor' => $email,
            'power' => $email,
            'power' => $email,
            'power' => $email,
            'power' => $email,
            'power' => $email,
            'power' => $email,
            'power' => $email,
            'power' => $email,'power' => $email,
            'power' => $email,'power' => $email,
            'power' => $email,
            'power' => $email,
            'power' => $email,
            'power' => $email,
            'power' => $email,
            'power' => $email,
            'power' => $email,
            'power' => $email,
            'power' => $email,'power' => $email,
            'power' => $email,
            
            
            
            
            'birthDate' => '12/10/1978'));
        $this->id = $I->grabFromDatabase('customers', 'id', array('fiscalId' => $this->fiscalId));
        $I->see('Saved');
    }
    public function updateVehicle(AcceptanceTester $I){
        
    }
    public function deleteVehicle(AcceptanceTester $I){
        
    }
}
