<?php 

class SellOffersCest
{
    protected $id;
    protected $customerId;
    protected $vehicleId;
    
    
    public function _before(FunctionalTester $I)
    {
        homeCest::loginTest($I);               
    }
    // tests
    public function SaveSellOfferTest(FunctionalTester $I)
    {
        $caracteres_permitidos = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $longitud = 7;        
        $fiscalId = substr(str_shuffle($caracteres_permitidos), 0, $longitud);        
        $I->amOnPage('/Intranet/admin');
        $I->click('Clientes', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/customers/list');
        $I->wantTo('Create a new Customer');
        $I->click('#submit', '#addCustomer');
        $I->seeCurrentUrlEquals('/Intranet/customers/form');
        $I->submitForm('#CustomerForm', array('fiscalId' => $fiscalId, 'name' => 'Lorem Ipsum', 'type' => 'Particular', 'address' => 'C/ Lorem ipsum, 5', 'city' => 'Lorem ipsum', 'postalCode' => '12345', 'state' => 'Lorem', 'country' => 'LoremIpsum', 'phone' => '65451386', 'email' => 'loremipsum@loremipsum.com', 'birth' => '12/06/1995'));          
        $this->customerId = $I->grabFromDatabase('customers', 'id', array('fiscalId' => $fiscalId));             
        $I->amOnPage('/Intranet/admin');
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
        $I->submitForm('#VehicleForm', array ('registry_date' => '02/04/2016','plate' => $matricula, 'vin' => $bastidor, 'brand' => 'FIAT', 'model' => 'DUCATO', 'description' => 'L3H2 130cv Furgón 35 3p', 'type' => 'Furgón', 'store' => 'Automotive', 'Kilómetros' => '45.728', 'power' => '130', 'places' => '3', 'doors' => '4', 'color' => 'Blanco', 'cost' => '10.000,00€', 'tvaBuy' => '2.100,00€', 'totalBuy' => '12.100,00€', 'pvp' => '12.500,00€', 'tvaSell' => '2.625,00€', 'totalSell' => '15.125,00€', 'acc-aire-acondicionado' => true));
        $this->vehicleId = $I->grabFromDatabase('vehicles', 'id', array('plate' => $matricula, 'vin' => $bastidor));
        $numero_serie = substr(str_shuffle($caracteres_permitidos), 0, $longitud);        
        $I->amOnPage('/Intranet/admin');
        $I->click('Componentes', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/vehicles/components/list');
        $I->wantTo('Create a new Component');
        $I->click('#submit', '#addComponent');
        $I->seeCurrentUrlEquals('/Intranet/vehicles/components/form');        
        $I->submitForm('#componentsForm', array('serialNumber' => $numero_serie,'ref' => 'loremipsum', 'mader' => 'Generico', 'name' => 'Lorem Ipsum', 'observations' => 'Lorem ipsum lorem ipsum lorem ipsum sumotas', 'pvc' => '10', 'pvp' => '16'));   
        $this->componentId = $I->grabFromDatabase('components', 'id', array('serialNumber' => $numero_serie)); 
        $referencia = substr(str_shuffle($caracteres_permitidos), 0, $longitud);        
        $I->amOnPage('/Intranet/admin');
        $I->click('Recambios', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/vehicles/supplies/list');
        $I->wantTo('Create a new Supply');
        $I->click('#submit', '#addSupply');
        $I->submitForm('#SupplyForm', 
                array('ref' => $referencia,
                    'mader' => 'Generico',
                    'mader_code' => 'loremipsum',
                    'name' => 'Lorem Ipsum',
                    'observations' => 'Lorem ipsum lorem ipsum lorem ipsum sumotas',
                    'pvc' => '10,00€',
                    'pvp' => '16,00€',
                    'tva_buy' => '2,10€',
                    'tva_sell' => '3,36€',
                    'total_buy' => '12,10€',
                    'total_sell' => '19,36€'
                    ));   
        $this->SupplyId = $I->grabFromDatabase('supplies', 'id', array('ref' => $referencia));
        $refWork = substr(str_shuffle($caracteres_permitidos), 0, $longitud);        
        $I->amOnPage('/Intranet/admin');
        $I->click('Trabajos', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/vehicles/works/list');
        $I->wantTo('Create a new Work');
        $I->click('#submit', '#addWork');
        $I->seeCurrentUrlEquals('/Intranet/vehicles/works/form');
        $I->submitForm('#WorkForm', 
                array('reference' => $refWork,                    
                    'description' => 'Lorem Ipsum',
                    'observations' => 'Lorem ipsum lorem ipsum lorem ipsum sumotas',                   
                    'price' => '16,00€',                    
                    'tva_sell' => '3,36€',                    
                    'total_sell' => '19,36€'
                    ));   
        $this->workId = $I->grabFromDatabase('works', 'id', array('reference' => $refWork));
        $I->amOnPage('/Intranet/admin');
        $I->click('Ofertas', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/sells/offers/list');
        $I->wantTo('Create a new SellOffer');
        $I->click('#submit', '#addSellOffer');        
        $I->seeCurrentUrlEquals('/Intranet/sells/offers/form');
        $I->submitForm('#SellOfferForm', array ('offerNumber' => '2021OF0001', 'offerDate' => Date('d/m/y', strtotime($I->grabValueFrom('#inputDate'))), 'texts' => 'Lorem ipsum lorem ipsum lorem ipsum sumotas', 'observations' => 'Lorem ipsum lorem ipsum lorem ipsum sumotas', 'customerId' => $this->customerId, 'vehicleId' => $this->vehicleId , 'discount' => $I->grabValueFrom('#inputDiscount'), 'pvp' => $I->grabValueFrom('#inputPrice'), 'tva' => $I->grabValueFrom('#inputTva'), 'total' => $I->grabValueFrom('#inputTotal'), 'vehiclePvp' => $I->grabValueFrom('#inputVehiclePrice'), 'vehicleTva' => $I->grabValueFrom('#inputVehicleTva'), 'vehicleTotal' => $I->grabValueFrom('#inputVehicleTotal'), 'vehicleComments' => $I->grabValueFrom('#inputVehicleTva')));
        $this->id = $I->grabFromDatabase('selloffers', 'id', array('offerNumber' => '2021OF0001'));
       
    }
    public function UpdateSellOfferTest(FunctionalTester $I)
    {
        $I->amOnPage('/Intranet/admin');
        $I->click('Ofertas', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/sells/offers/list');
        $I->wantTo('Update SellOffer');
        $I->click('#submit', '#addSellOffer');        
        $I->seeCurrentUrlEquals('/Intranet/sells/offers/form'.$this->id);
        $I->click('#submit'); 
        $I->see('Updated');
            
    }
    public function DeleteSellOfferTest(FunctionalTester $I)
    {
       
    }
}
