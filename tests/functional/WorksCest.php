<?php 

class WorksCest
{
    protected $id;
    
    public function _before(FunctionalTester $I)
    {
        homeCest::loginTest($I);
    }

    // tests
    public function SaveWorkTest(FunctionalTester $I)
    {
        $caracteres_permitidos = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $longitud = 7;        
        $referencia = substr(str_shuffle($caracteres_permitidos), 0, $longitud);        
        $I->amOnPage('/intranet/admin');
        $I->click('Trabajos', '.list-group-item');
        $I->seeCurrentUrlEquals('/intranet/vehicles/works/list');
        $I->wantTo('Create a new Work');
        $I->click('#submit', '#addWork');
        $I->submitForm('#WorkForm', 
                array('reference' => $referencia,                    
                    'description' => 'Lorem Ipsum',
                    'observations' => 'Lorem ipsum lorem ipsum lorem ipsum sumotas',                   
                    'price' => '16,00€',                    
                    'tva_sell' => '3,36€',                    
                    'total_sell' => '19,36€'
                    ));   
        $this->id = $I->grabFromDatabase('works', 'id', array('reference' => $referencia));
        $I->canSee('Saved');
    }
    public function UpdateWorkTest(FunctionalTester $I)
    {
        $I->amOnPage('/intranet/admin');
        $I->click('Trabajos', '.list-group-item');
        $I->seeCurrentUrlEquals('/intranet/vehicles/works/list');
        $I->wantTo('Update Work');
        $I->amOnPage('/intranet/vehicles/works/form?id='.$this->id);
        $I->click('#submit');
        $I->canSee('Updated');     
    }
    public function DeleteWorkTest(FunctionalTester $I)
    {
        $I->amOnPage('/intranet/admin');
        $I->click('Trabajos', '.list-group-item');
        $I->seeCurrentUrlEquals('/intranet/vehicles/works/list');
        $I->wantTo('Delete Work');
        $I->amOnPage('/intranet/vehicles/works/delete?id='.$this->id);
        $I->canSee('Trabajos');
    }
}
