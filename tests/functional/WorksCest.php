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
        $I->amOnPage('/Intranet/admin');
        $I->click('Trabajos', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/vehicles/works/list');
        $I->wantTo('Create a new Work');
        $I->click('#submit', '#addWork');
        $I->seeCurrentUrlEquals('/Intranet/vehicles/works/form');
        $I->submitForm('#WorkForm', 
                array('reference' => $referencia,                    
                    'description' => 'Lorem Ipsum',
                    'observations' => 'Lorem ipsum lorem ipsum lorem ipsum sumotas',                   
                    'price' => '16,00€',                    
                    'tva_sell' => '3,36€',                    
                    'total_sell' => '19,36€'
                    ));   
        $this->id = $I->grabFromDatabase('works', 'id', array('reference' => $referencia));
        
    }
    public function UpdateWorkTest(FunctionalTester $I)
    {
        $I->amOnPage('/Intranet/admin');
        $I->click('Trabajos', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/vehicles/works/list');
        $I->wantTo('Update Work');
        $I->amOnPage('/Intranet/vehicles/works/form?id='.$this->id);
        $I->click('#submit');
           
    }
    public function DeleteWorkTest(FunctionalTester $I)
    {
        $I->amOnPage('/Intranet/admin');
        $I->click('Trabajos', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/vehicles/works/list');
        $I->wantTo('Delete Work');
        $I->amOnPage('/Intranet/vehicles/works/delete?id='.$this->id);
        
    }
}
