<?php 

class ComponentsCest
{
    protected $id;
    
    public function _before(FunctionalTester $I)
    {
        homeCest::loginTest($I);
    }
    // tests
    public function SaveComponentTest(FunctionalTester $I)
    {
        $caracteres_permitidos = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $longitud = 7;        
        $numero_serie = substr(str_shuffle($caracteres_permitidos), 0, $longitud);        
        $I->amOnPage('/Intranet/admin');
        $I->click('Componentes', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/buys/components/list');
        $I->wantTo('Create a new Component');
        $I->click('#submit', '#addComponent');
        $I->seeCurrentUrlEquals('/Intranet/buys/components/form');        
        $I->submitForm('#component', array('serialNumber' => $numero_serie,'ref' => 'loremipsum', 'mader' => 'Generico', 'name' => 'Lorem Ipsum', 'observations' => 'Lorem ipsum lorem ipsum lorem ipsum sumotas', 'pvc' => '10', 'pvp' => '16'));   
        $this->id = $I->grabFromDatabase('components', 'id', array('serialNumber' => $numero_serie));        
       
    }
    public function UpdateComponentTest(FunctionalTester $I)
    {     
        $I->amOnPage('/Intranet/admin');
        $I->click('Componentes', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/buys/components/list');
        $I->wantTo('Update Component');
        $I->amOnPage('/Intranet/buys/components/form?id='.$this->id);
        $I->click('#submit');
             
    }
    public function DeleteComponentTest(FunctionalTester $I)
    {
        $I->amOnPage('/Intranet/admin');
        $I->click('Componentes', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/buys/components/list');
        $I->wantTo('Delete Component');
        $I->amOnPage('/Intranet/buys/components/delete?id='.$this->id);
     
    }
}
