<?php 

namespace Tests\acceptance;

use AcceptanceTester;
use Tests\acceptance\FirstCest;

class WorksCest
{
    protected $id; 
    protected $reference;
    public function _before(AcceptanceTester $I) {
        FirstCest::loginTest($I);
    }
    // tests
    public function accesWork(AcceptanceTester $I){
        $I->amOnPage('/Intranet/admin');
        $I->click('Trabajos', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/vehicles/works/list');
        $I->wantTo('Acces Works Pages');
        $I->click('#submit', '#addWork');
        $I->seeCurrentUrlEquals('/Intranet/vehicles/works/form');        
    }
    public function saveWorkTest(AcceptanceTester $I) {
        $caracteres_permitidos = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $longitud = 7;        
        $this->reference = substr(str_shuffle($caracteres_permitidos), 0, $longitud);        
        $I->amOnPage('/Intranet/admin');
        $I->click('Trabajos', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/vehicles/works/list');
        $I->wantTo('Create a new Work');
        $I->click('#submit', '#addWork');
        $I->submitForm('#worksForm', 
                array('reference' => $this->reference,                    
                    'description' => 'loremipsum',                    
                    'observations' => 'Lorem ipsum lorem ipsum lorem ipsum sumotas',
                    'pvc' => '10,00€',
                    'pvp' => '16,00€'));   
        $this->id = $I->grabFromDatabase('works', 'id', array('reference' => $this->reference));
        $I->see('Saved');     
    }
    public function updateWorkTest(AcceptanceTester $I) {
        $I->amOnPage('/Intranet/admin');
        $I->click('Trabajos', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/vehicles/works/list');
        $I->wantTo('Update Work');
        $I->amOnPage('/Intranet/vehicles/works/form?id='.$this->id);
        $I->submitForm('#worksForm', 
                array('id' => $this->id,
                    'reference' => $this->reference,                    
                    'description' => 'loremipsum',                    
                    'observations' => 'Lorem ipsum lorem ipsum lorem ipsum sumotas',
                    'pvc' => '10,00€',
                    'pvp' => '16,00€'));   
        $I->see('Updated');
            
    }
    public function deleteWorkTest(AcceptanceTester $I) {
        $I->amOnPage('/Intranet/admin');
        $I->click('Trabajos', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/vehicles/works/list');
        $I->wantTo('Delete Work');
        $I->amOnPage('/Intranet/vehicles/works/delete?id='.$this->id);
        $I->dontSeeInDatabase('works', array('id' => intval($this->id), 'deleted_at' => null));
    }
}
