<?php 

namespace Tests\acceptance;

use AcceptanceTester;
use Tests\acceptance\FirstCest;

class ComponentsCest
{
    protected $id; 
    protected $reference;
    protected $serialNumber;
    public function _before(AcceptanceTester $I) {
        FirstCest::loginTest($I);
    }
    // tests
    public function accesComponent(AcceptanceTester $I){
        $I->amOnPage('/Intranet/admin');
        $I->click('Componentes', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/vehicles/components/list');
        $I->wantTo('Acces Components Pages');
        $I->click('#submit', '#addComponent');
        $I->seeCurrentUrlEquals('/Intranet/vehicles/components/form');        
    }
    public function saveComponentTest(AcceptanceTester $I) {
        $caracteres_permitidos = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $longitud = 7;        
        $this->reference = substr(str_shuffle($caracteres_permitidos), 0, $longitud); 
        $this->serialNumber = substr(str_shuffle($caracteres_permitidos), 0, $longitud*2); 
        $I->amOnPage('/Intranet/admin');
        $I->click('Componentes', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/vehicles/components/list');
        $I->wantTo('Create a new Component');
        $I->click('#submit', '#addComponent');
        $I->submitForm('#componentsForm', 
                array('ref' => $this->reference,
                    'mader' => 'Generico',
                    'serialNumber' => $this->serialNumber,
                    'name' => 'Lorem Ipsum',
                    'observations' => 'Lorem ipsum lorem ipsum lorem ipsum sumotas',
                    'pvc' => '10,00€',
                    'pvp' => '16,00€'));   
        $this->id = $I->grabFromDatabase('components', 'id', array('serialNumber' => $this->serialNumber));
        $I->see('Saved');     
    }
    public function updateComponentTest(AcceptanceTester $I) {
        $I->amOnPage('/Intranet/admin');
        $I->click('Componentes', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/vehicles/components/list');
        $I->wantTo('Update Component');
        $I->amOnPage('/Intranet/vehicles/components/form?id='.$this->id);
       $I->submitForm('#componentsForm', 
                array('ref' => $this->reference,
                    'mader' => 'Generico',
                    'serialNumber' => $this->serialNumber,
                    'name' => 'Lorem Ipsum',
                    'observations' => 'Lorem ipsum lorem ipsum lorem ipsum sumotas',
                    'pvc' => '10,00€',
                    'pvp' => '16,00€'));  
        $I->see('Updated');
            
    }
    public function deleteComponentTest(AcceptanceTester $I)  {
        $I->amOnPage('/Intranet/admin');
        $I->click('Componentes', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/vehicles/components/list');
        $I->wantTo('Delete Component');
        $I->amOnPage('/Intranet/vehicles/components/delete?id='.$this->id);
        $I->dontSeeInDatabase('components', array('id' => intval($this->id), 'deleted_at' => null));    
    }
}
