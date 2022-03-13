<?php 

namespace Tests\acceptance;

use AcceptanceTester;
use Tests\acceptance\FirstCest;

class SuppliesCest
{
    protected $id; 
    protected $reference;
    public function _before(AcceptanceTester $I) {
        FirstCest::loginTest($I);
    }
    // tests
    public function accesSupply(AcceptanceTester $I){
        $I->amOnPage('/Intranet/admin');
        $I->click('Recambios', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/vehicles/supplies/list');
        $I->wantTo('Acces Supplies Pages');
        $I->click('#submit', '#addSupply');
        $I->seeCurrentUrlEquals('/Intranet/vehicles/supplies/form');        
    }
    public function saveSupplyTest(AcceptanceTester $I) {
        $caracteres_permitidos = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $longitud = 7;        
        $this->reference = substr(str_shuffle($caracteres_permitidos), 0, $longitud);        
        $I->amOnPage('/Intranet/admin');
        $I->click('Recambios', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/vehicles/supplies/list');
        $I->wantTo('Create a new Supply');
        $I->click('#submit', '#addSupply');
        $I->submitForm('#suppliesForm', 
                array('name' => 'Lorem Ipsum',
                    'ref' => $this->reference,
                    'mader' => 'Generico',
                    'maderCode' => 'loremipsum',                    
                    'observations' => 'Lorem ipsum lorem ipsum lorem ipsum sumotas',
                    'pvc' => '10,00€',
                    'pvp' => '16,00€'));   
        $this->id = $I->grabFromDatabase('supplies', 'id', array('ref' => $this->reference));
        $I->see('Saved');     
    }
    public function updateSupplyTest(AcceptanceTester $I) {
        $I->amOnPage('/Intranet/admin');
        $I->click('Recambios', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/vehicles/supplies/list');
        $I->wantTo('Update Supply');
        $I->amOnPage('/Intranet/vehicles/supplies/form?id='.$this->id);
        $I->submitForm('#suppliesForm', 
                array('id' => $this->id,
                    'name' => 'Lorem Ipsum',
                    'ref' => $this->reference,
                    'mader' => 'Generico',
                    'maderCode' => 'loremipsum',                    
                    'observations' => 'Lorem ipsum lorem ipsum lorem ipsum sumotas',
                    'pvc' => '10,00€',
                    'pvp' => '16,00€'));  
        $I->see('Updated');
            
    }
    public function deleteSupplyTest(AcceptanceTester $I)
    {
        $I->amOnPage('/Intranet/admin');
        $I->click('Recambios', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/vehicles/supplies/list');
        $I->wantTo('Delete Supply');
        $I->amOnPage('/Intranet/vehicles/supplies/delete?id='.$this->id);
        $I->dontSeeInDatabase('supplies', array('id' => intval($this->id), 'deleted_at' => null));    
    }
}
