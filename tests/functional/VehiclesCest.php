<?php 

class VehiclesCest
{
    protected $id;
    
    public function _before(FunctionalTester $I){
          homeCest::loginTest($I);
    }

    // tests
    public function SaveVehicleTest(FunctionalTester $I){
        $caracteres_permitidos = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $longitud_placa = 7;
        $longitud_bastidor = 10;
        $matricula = substr(str_shuffle($caracteres_permitidos), 0, $longitud_placa);
        $bastidor = substr(str_shuffle($caracteres_permitidos), 0, $longitud_bastidor);
        $I->amOnPage('/Intranet/admin');
        $I->click('Vehículos', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/vehicles/list');
        $I->wantTo('Create a new Vehicle');
        $I->click('#submit', '#addVehicle');
        $I->seeCurrentUrlEquals('/Intranet/vehicles/form');        
        $I->submitForm('#VehicleForm', array ('registry_date' => '02/04/2016','plate' => $matricula, 'vin' => $bastidor, 'brand' => 'FIAT', 'model' => 'DUCATO', 'description' => 'L3H2 130cv Furgón 35 3p', 'type' => 'Furgon', 'store' => 'Automotive', 'Kilómetros' => '45.728', 'power' => '130', 'places' => '3', 'doors' => '4', 'color' => 'Blanco', 'cost' => '10.000,00€', 'tvaBuy' => '2.100,00€', 'totalBuy' => '12.100,00€', 'pvp' => '12.500,00€', 'tvaSell' => '2.625,00€', 'totalSell' => '15.125,00€', 'acc-aire-acondicionado' => true));
        $this->id = $I->grabFromDatabase('vehicles', 'id', array('plate' => $matricula, 'vin' => $bastidor));
      
    }
    public function UpdateVehicleTest(FunctionalTester $I){
        $I->amOnPage('/Intranet/admin');
        $I->click('Vehículos', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/vehicles/list');
        $I->wantTo('Update Vehicle');
        $I->amOnPage('/Intranet/vehicles/form?id='.$this->id);
        $I->see('Vehiculo');
        $I->submitForm('#VehicleForm', array('places' => '6'));
             
    }
    public function DeleteVehicleTest(FunctionalTester $I){
//        $I->amOnPage('/Intranet/admin');
//        $I->click('Vehículos', '.list-group-item');
//        $I->seeCurrentUrlEquals('/Intranet/vehicles/list');
//        $I->wantTo('Delete Vehicle');
//        $I->amOnPage('/Intranet/vehicle/delete?id='.$this->id);              
    }
}
