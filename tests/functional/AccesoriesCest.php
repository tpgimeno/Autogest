<?php 

class AccesoriesCest
{
    protected $id;
    
    public function _before(FunctionalTester $I)
    {
        homeCest::loginTest($I);
    }

    // tests
    public function SaveAccesoryTest(FunctionalTester $I)
    {
        $I->amOnPage('/Intranet/admin');
        $I->click('Accesorios', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/vehicles/accesories/list');
        $I->wantTo('Create a new Accesory');
        $I->click('#submit', '#addAccesory');
        $I->submitForm('#AccesoryForm', array('name' => 'Lorem Ipsum'));   
        $this->id = $I->grabFromDatabase('accesories', 'id', array('name' => 'Lorem Ipsum'));
        $I->canSee('Saved');
    }
    public function UpdateAccesoryTest(FunctionalTester $I)
    {
        $I->amOnPage('/Intranet/admin');
        $I->click('Accesorios', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/vehicles/accesories/list');
        $I->wantTo('Update Accesory');
        $I->amOnPage('/Intranet/vehicles/accesories/form?id='.$this->id);
        $I->click('#submit');
        $I->canSee('Updated');
     
    }
    
    public function DeleteAccesoryTest(FunctionalTester $I)
    {
        
        $I->amOnPage('/Intranet/admin');
        $I->click('Accesorios', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/vehicles/accesories/list');
        $I->wantTo('Delete Accesory');
        $I->amOnPage('/Intranet/vehicles/accesories/delete?id='.$this->id);        
    }
}
