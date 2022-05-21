<?php

namespace Tests\acceptance;

use AcceptanceTester;

use Tests\acceptance\FirstCest;


class VehiclesCest {
    protected $id;
    protected $brandName;  
    protected $brandId;
    protected $modelId; 
    protected $modelName;
    protected $typeName;  
    protected $typeId;
    protected $storeName;   
    protected $storesId;
    protected $locationName;   
    protected $locationsId;
    protected $providorFiscalId;
    protected $providorsId;
    protected $providorName;
    protected $customerFiscalId;
    protected $customerId;
    protected $customerName;
    protected $sellerName;
    protected $sellerFiscalId;
    protected $sellerId;  
   
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
        $I->click('Marcas', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/vehicles/brands/list');
        $caracteres_permitidos = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';       
        $longitudBrand = 8;        
        $this->brandName = substr(str_shuffle($caracteres_permitidos), 0, $longitudBrand);       
        $I->click('#submit', '#addBrand');
        $I->seeCurrentUrlEquals('/Intranet/vehicles/brands/form');       
        $I->submitForm('#brandsForm', array('name' => $this->brandName));        
        $I->see('Saved');
        $this->brandId = $I->grabFromDatabase('brands', 'id', array('id' => $this->brandName));
        $I->amOnPage('/Intranet/admin');
        $I->click('Modelos', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/vehicles/models/list');   
        $longitudModel = 8;        
        $this->modelName = substr(str_shuffle($caracteres_permitidos), 0, $longitudModel);        
        $I->click('#submit', '#addModel');
        $I->seeCurrentUrlEquals('/Intranet/vehicles/models/form');        
        $I->submitForm('#modelsForm', array('name' => $this->modelName, 'brand' => $this->brandName)); 
        $I->see('Saved');
        $this->modelId = $I->grabFromDatabase('models', 'id', array('name' => $this->modelName));                
        $I->amOnPage('/Intranet/admin');
        $I->click('Almacenes', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/stores/list');               
        $longitudStoreEmail = 10;
        $longitudStore = 6;
        $email = substr(str_shuffle($caracteres_permitidos), 0, $longitudStoreEmail)."@".substr(str_shuffle($caracteres_permitidos), 0, $longitudStoreEmail).".com";
        $this->storeName = substr(str_shuffle($caracteres_permitidos), 0, $longitudStore);        
        $I->click('#submit', '#addStore');
        $I->seeCurrentUrlEquals('/Intranet/stores/form'); 
        $I->submitForm('#storesForm', array ('name' => $this->storeName,
            'address' => 'Lorem ipsum, 25',
            'city' => 'Ipsum', 
            'postal_code' => 15874, 
            'state' => 'Lorem', 
            'country' => 'LoremIpsum',
            'phone' => 95874581,
            'email' => $email));
        $I->see('Saved');
        $this->storeId = $I->grabFromDatabase('stores', 'id', array('name' => $this->storeName));
        $I->amOnPage('/Intranet/admin');
        $I->click('Ubicaciones', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/locations/list');               
        $longitudLocation = 2;        
        $this->locationName = substr(str_shuffle($caracteres_permitidos), 0, $longitudLocation);        
        $I->click('#submit', '#addLocation');
        $I->seeCurrentUrlEquals('/Intranet/locations/form');        
        $I->submitForm('#locationForm', array('name' => $this->locationName, 'store' => $this->storeName)); 
        $I->see('Saved');        
        $this->locationId = $I->grabFromDatabase('locations', 'id', array('name' => $this->locationName, 'storeId' => $this->storeId));                
        $I->amOnPage('/Intranet/admin');
        $I->click('Tipos Vehículo', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/vehicles/vehicleTypes/list');                
        $longitudTypes = 8;        
        $this->typeName = substr(str_shuffle($caracteres_permitidos), 0, $longitudTypes);        
        $I->click('#submit', '#addVehicleType');
        $I->seeCurrentUrlEquals('/Intranet/vehicles/vehicleTypes/form');       
        $I->submitForm('#vehicleTypesForm', array('name' => $this->typeName));
        $I->see('Saved');          
        $this->typeId = $I->grabFromDatabase('vehicletypes', 'id', array('name' => $this->typeName));                     
        $I->amOnPage('/Intranet/admin');
        $I->click('Proveedores', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/buys/providers/list');                
        $longitudProvidorsEmail = 10;   
        $longitudProvidors = 10;
        $providorEmail = substr(str_shuffle($caracteres_permitidos), 0, $longitudProvidorsEmail)."@".substr(str_shuffle($caracteres_permitidos), 0, $longitudProvidorsEmail).".com";
        $this->providorFiscalId = substr(str_shuffle($caracteres_permitidos), 0, $longitudProvidors);        
        $I->click('#submit', '#addProvidor');
        $I->seeCurrentUrlEquals('/Intranet/buys/providers/form'); 
        $I->submitForm('#providersForm', array ('name' => 'Lorem',
            'fiscalId' => $this->providorFiscalId,
            'fiscalName' => 'LoremIpsum',
            'address' => 'Lorem ipsum, 25',
            'city' => 'Ipsum', 
            'postalCode' => 15874, 
            'state' => 'Lorem', 
            'country' => 'LoremIpsum',
            'phone' => 95874581,
            'email' => $providorEmail));
        $I->see('Saved');
        $this->providorsId = $I->grabFromDatabase('providers', 'id', array('fiscalId' => $this->providorFiscalId));
        $this->providorName = $I->grabFromDatabase('providers', 'name', array('id' => $this->providorsId));
        $I->amOnPage('/Intranet/admin');
        $I->click('Clientes', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/customers/list');                
        $longitudCustomerEmail = 10;
        $longitudCustomer = 10;
        $customerEmail = substr(str_shuffle($caracteres_permitidos), 0, $longitudCustomerEmail)."@".substr(str_shuffle($caracteres_permitidos), 0, $longitudCustomerEmail).".com";
        $this->customerFiscalId = substr(str_shuffle($caracteres_permitidos), 0, $longitudCustomer);        
        $I->click('#submit', '#addCustomer');
        $I->seeCurrentUrlEquals('/Intranet/customers/form'); 
        $I->submitForm('#customersForm', array ('name' => 'Lorem', 
            'fiscalId' => $this->customerFiscalId, 
            'customerType' => 'PARTICULAR',
            'address' => 'Lorem ipsum, 25',
            'city' => 'Ipsum', 
            'postalCode' => 15874, 
            'state' => 'Lorem', 
            'country' => 'LoremIpsum',
            'phone' => 95874581,
            'email' => $customerEmail,
            'birthDate' => '12/10/1978'));
        $I->see('Saved'); 
        $this->customerId = $I->grabFromDatabase('customers', 'id', array('fiscalId' => $this->customerFiscalId));   
        $this->customerName = $I->grabFromDatabase('customers', 'name', array('id' => $this->customerId));
        $I->amOnPage('/Intranet/admin');
        $I->click('Comerciales', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/sellers/list');              
        $longitudSellerEmail = 10; 
        $longitudSeller = 10;         
        $sellerEmail = substr(str_shuffle($caracteres_permitidos), 0, $longitudSellerEmail)."@".substr(str_shuffle($caracteres_permitidos), 0, $longitudSellerEmail).".com";
        $this->sellerFiscalId = substr(str_shuffle($caracteres_permitidos), 0, $longitudSeller);        
        $I->wantTo('Create a new Seller');
        $I->click('#submit', '#addSeller');
        $I->seeCurrentUrlEquals('/Intranet/sellers/form'); 
        $I->submitForm('#sellersForm', array ('name' => 'Lorem', 
            'fiscalId' => $this->sellerFiscalId,                      
            'address' => 'Lorem ipsum, 25',
            'city' => 'Ipsum', 
            'postalCode' => 15874, 
            'state' => 'Lorem', 
            'country' => 'LoremIpsum',
            'phone' => 95874581,
            'email' => $sellerEmail,
            'birthDate' => '12/10/1978'));
        $I->see('Saved');
        $this->sellerId = $I->grabFromDatabase('sellers', 'id', array('fiscalId' => $this->sellerFiscalId));
        $this->sellerName = $I->grabFromDatabase('sellers', 'name', array('id' => $this->sellerId));
        $I->amOnPage('/Intranet/admin');
        $I->click('Vehículos', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/vehicles/list');               
        $longitud = 10;         
        $matricula = substr(str_shuffle($caracteres_permitidos), 0, $longitud);
        $vin = substr(str_shuffle($caracteres_permitidos), 0, $longitud);       
        $I->wantTo('Create a new Vehicle');
        $I->click('#submit', '#addVehicle');
        $I->seeCurrentUrlEquals('/Intranet/vehicles/form'); 
        $I->submitForm('#vehiclesForm', array ('plate' => $matricula, 
            'vin' => $vin, 
            'brand' => $this->brandName,
            'model' => $this->modelName,
            'description' => 'Berlingo 1.6HDI 90cv', 
            'type' => $this->typeName, 
            'store' => $this->storeName, 
            'location' => $this->locationName,
            'km' => 73524,
            'power' => 90,
            'places' => 2,
            'doors' => 4,
            'providor' => $this->providorName,
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
            'customer' => $this->customerName,
            'seller' => $this->sellerName,
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
          
    }
    public function updateVehicle(AcceptanceTester $I){
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
        $I->submitForm('#vehiclesForm', array ('id' => $this->id,
            'plate' => $matricula, 
            'vin' => $vin, 
            'brand' => $this->brandName,
            'model' => $this->modelName,
            'description' => 'Berlingo 1.6HDI 90cv Expression', 
            'type' => $this->typeName, 
            'store' => $this->storeName, 
            'location' => $this->locationName,
            'km' => 84000,
            'power' => 90,
            'places' => 2,
            'doors' => 4,
            'providor' => $this->providorName,
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
            'customer' => $this->customerName,
            'seller' => $this->sellerName,
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
        $I->see('Updated');
    }
    public function deleteVehicle(AcceptanceTester $I){
        $I->wantTo('Delete Vehicle');
        $I->amOnPage('/Intranet/admin');
        $I->click('Vehículos', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/vehicles/list');
        $I->amOnPage('/Intranet/vehicles/delete?id='.$this->id);
        $I->amOnPage('/Intranet/brands/delete?id='.$this->brandId);
        $I->amOnPage('/Intranet/models/delete?id='.$this->modelId);
        $I->amOnPage('/Intranet/locations/delete?id='.$this->locationsId);
        $I->amOnPage('/Intranet/stores/delete?id='.$this->storeId);
        $I->amOnPage('/Intranet/vehicles/vehicleTypes/delete?id='.$this->typeId);
        $I->amOnPage('/Intranet/buys/providers/delete?id='.$this->providorsId);
        $I->amOnPage('/Intranet/customers/delete?id='.$this->customerId);
        $I->amOnPage('/Intranet/sellers/delete?id='.$this->sellerId);        
        $I->dontSeeInDatabase('vehicles', array('id' => intval($this->id), 'deleted_at' => null));
    }
   
}
