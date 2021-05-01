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
        $I->amOnPage('/intranet/admin');
        $I->click('Componentes', '.list-group-item');
        $I->seeCurrentUrlEquals('/intranet/buys/components/list');
        $I->wantTo('Create a new Component');
        $I->click('#submit', '#addComponent');
        $I->submitForm('#ComponentForm', 
                array('serial_number' => $numero_serie,
                    'ref' => 'loremipsum',
                    'mader' => 'Generico',                    
                    'name' => 'Lorem Ipsum',
                    'observations' => 'Lorem ipsum lorem ipsum lorem ipsum sumotas',
                    'pvc' => '10,00€',
                    'pvp' => '16,00€',
                    'tva_buy' => '2,10€',
                    'tva_sell' => '3,36€',
                    'total_buy' => '12,10€',
                    'total_sell' => '19,36€'
                    ));   
        $this->id = $I->grabFromDatabase('components', 'id', array('serial_number' => $numero_serie));
        $I->canSee('Saved');
    }
    public function UpdateComponentTest(FunctionalTester $I)
    {
        $I->amOnPage('/intranet/admin');
        $I->click('Componentes', '.list-group-item');
        $I->seeCurrentUrlEquals('/intranet/buys/components/list');
        $I->wantTo('Update Component');
        $I->amOnPage('/intranet/buys/components/form?id='.$this->id);
        $I->click('#submit');
        $I->canSee('Updated');     
    }
    public function DeleteComponentTest(FunctionalTester $I)
    {
        $I->amOnPage('/intranet/admin');
        $I->click('Componentes', '.list-group-item');
        $I->seeCurrentUrlEquals('/intranet/buys/components/list');
        $I->wantTo('Delete Component');
        $I->amOnPage('/intranet/buys/components/delete?id='.$this->id);
        $I->canSee('Componentes');
    }
}
