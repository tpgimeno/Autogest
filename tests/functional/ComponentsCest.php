<?php

class ComponentsCest
{
    protected $id;
    
    public function _before(FunctionalTester $I)
    {
        homeCest::loginTest($I);
        $mader = $I->grabFromDatabase('maders', 'name', array('name' => 'Generico'));
        if(!$mader){
            $I->amOnPage('/Intranet/admin');
            $I->click('Fabricantes', '.list-group-item');
            $I->seeCurrentUrlEquals('/Intranet/buys/maders/list');
            $I->click('#submit', '#addMader');
            $I->submitForm('#madersForm', array('name' => 'Generico'));
            $I->canSee('Saved');
        }
    }
    // tests
    public function SaveComponentTest(FunctionalTester $I)
    {
        $caracteres_permitidos = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $longitud = 7;        
        $numero_serie = substr(str_shuffle($caracteres_permitidos), 0, $longitud);        
        $I->amOnPage('/Intranet/admin');
        $I->click('Componentes', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/vehicles/components/list');
        $I->wantTo('Create a new Component');
        $I->click('#submit', '#addComponent');
        $I->seeCurrentUrlEquals('/Intranet/vehicles/components/form');        
        $I->submitForm('#componentsForm', array('name' => 'Lorem Ipsum', 'ref' => 'loremipsum', 'serialNumber' => $numero_serie, 'mader' => 'Generico', 'observations' => 'Lorem ipsum lorem ipsum lorem ipsum sumotas', 'pvc' => '10', 'pvp' => '16'));   
        $this->id = $I->grabFromDatabase('components', 'id', array('serialNumber' => $numero_serie));  
        $I->canSee('Saved');
       
    }
    public function UpdateComponentTest(FunctionalTester $I)
    {     
        $I->amOnPage('/Intranet/admin');
        $I->click('Componentes', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/vehicles/components/list');
        $I->wantTo('Update Component');
        $I->amOnPage('/Intranet/vehicles/components/form?id='.$this->id);
        $I->click('#submit');
        $I->canSee('Updated');
             
    }
    public function DeleteComponentTest(FunctionalTester $I)
    {
        $I->amOnPage('/Intranet/admin');
        $I->click('Componentes', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/vehicles/components/list');
        $I->wantTo('Delete Component');
        $I->amOnPage('/Intranet/vehicles/components/delete?id='.$this->id);
     
    }
}
