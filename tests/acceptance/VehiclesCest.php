<?php

namespace Tests\acceptance;

use AcceptanceTester;
use Tests\acceptance\BrandsCest;
use Tests\acceptance\FirstCest;
use Tests\acceptance\LocationsCest;
use Tests\acceptance\ModelsCest;
use Tests\acceptance\ProvidersCest;
use Tests\acceptance\StoresCest;
use Tests\acceptance\VehicleTypesCest;

class VehiclesCest {
    protected $id;
    protected $brand;  
    protected $brandsCest;
    protected $model; 
    protected $modelsCest;
    protected $type;  
    protected $vehicleTypesCest;
    protected $store;   
    protected $storesCest;
    protected $location;   
    protected $locationsCest;
    protected $providor;
    protected $providorsCest;
    protected $customer;
    protected $customerCest;
    protected $seller;
    protected $sellerCest;  
   
    public function _before(AcceptanceTester $I) {
        FirstCest::loginTest($I);       
        
    }
    // tests
    public function accessVehicle(AcceptanceTester $I) {
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
        $this->brandsCest = new BrandsCest();
        $this->brandsCest->saveBrandTest($I);               
        $this->brand = $I->grabFromDatabase('brands', 'name', array('id' => $this->brandsCest->id));  
        $I->amOnPage('/Intranet/admin');
        $this->modelsCest = new ModelsCest();
        $this->modelsCest->saveModelTest($I);
        $this->model = $I->grabFromDatabase('models', 'name', array('id' => $this->modelsCest->id));          
        $this->storesCest = new StoresCest();
        $this->storesCest->saveStoreTest($I);        
        $this->store = $I->grabFromDatabase('stores', 'name', array('id' => $this->storesCest->id));         
        $this->vehicleTypesCest = new VehicleTypesCest();
        $this->vehicleTypesCest->saveVehicleTypeTest($I);        
        $this->type = $I->grabFromDatabase('vehicletypes', 'name', array('id' => $this->vehicleTypesCest->id));        
        $this->locationsCest = new LocationsCest();
        $this->locationsCest->saveLocationTest($I);        
        $this->location = $I->grabFromDatabase('locations', 'name', array('id' => $this->locationsCest->id));        
        $this->providorsCest = new ProvidersCest();
        $this->providorsCest->saveProvidorTest($I);            
        $this->providor = $I->grabFromDatabase('providers', 'name', array('id' => $this->providorsCest->id)); 
        $this->customerCest = new CustomersCest();
        $this->customerCest->saveCustomerTest($I);
        $this->customer = $I->grabFromDatabase('customers', 'name', array('id' => $this->customerCest->id));
        $this->sellerCest = new SellersCest();
        $this->sellerCest->saveSellerTest($I);
        $this->seller = $I->grabFromDatabase('sellers', 'name', array('id' => $this->sellerCest->id));
        $I->amOnPage('/Intranet/admin');
        $I->click('Vehículos', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/vehicles/list');
        $caracteres_permitidos = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';        
        $longitud = 10;         
        $matricula = substr(str_shuffle($caracteres_permitidos), 0, $longitud);
        $vin = substr(str_shuffle($caracteres_permitidos), 0, $longitud);       
        $I->wantTo('Create a new Vehicle');
        $I->click('#submit', '#addVehicle');
        $I->seeCurrentUrlEquals('/Intranet/vehicles/form'); 
        $I->submitForm('#vehiclesForm', array ('plate' => $matricula, 
            'vin' => $vin, 
            'brand' => $this->brand,
            'model' => $this->model,
            'description' => 'Berlingo 1.6HDI 90cv', 
            'type' => $this->type, 
            'store' => $this->store, 
            'location' => $this->location,
            'km' => 73524,
            'power' => 90,
            'places' => 2,
            'doors' => 4,
            'providor' => $this->providor,
            'arrival' => '12/05/2018',
            'dateBuy' => '10/05/2018',
            'transference' => null,
            'service' => 'particular',
            'secondKey' => 1,
            'rebu' => null,
            'technicCard' => null,
            'permission' => 1,
            'cost' => '9528,56',
            'pvp' => '10500',
            'sellDate' => null,
            'appointDate' => null,
            'dataType' => null,
            'variant' => null,
            'version' => null,
            'comercialName' => null,
            'mma' => 2100,
            'mmaAxe1' => 950,
            'mmaAxe2' => 1150,
            'mmac' => 4600,
            'mmar' => 750,
            'mmarf' => 2500,
            'mom' => 1379,
            'momAxe1' => 756,
            'momAxe2' => 623,
            'large' => 4980,
            'width' => 2150,
            'height' => 1786,
            'frontOverhang' => 750,
            'rearOverhang' => 830,
            'axeDistance' => 3400,
            'chargeLength' => 1820,
            'deposit' => 55,
            'initCharge' => 1240));
        $this->id = $I->grabFromDatabase('vehicles', 'id', array('plate' => $matricula));
        $I->see('Saved');
        $this->brandsCest->deleteBrandTest($I);
        $this->modelsCest->deleteModelTest($I);
        $this->vehicleTypesCest->deleteVehicleTypeTest($I);
        $this->storesCest->deleteStoreTest($I);
        $this->locationsCest->deleteLocationTest($I);
        $this->providorsCest->deleteProvidorTest($I);    
    }
    public function updateVehicle(AcceptanceTester $I){
        
    }
    public function deleteVehicle(AcceptanceTester $I){
        
    }
   
}
