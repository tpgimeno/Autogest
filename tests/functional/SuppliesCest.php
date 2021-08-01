<?php 

class SuppliesCest
{
   protected $id;
    
    public function _before(FunctionalTester $I)
    {
        homeCest::loginTest($I);
    }

    // tests
    public function SaveSupplyTest(FunctionalTester $I)
    {
        $caracteres_permitidos = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $longitud = 7;        
        $referencia = substr(str_shuffle($caracteres_permitidos), 0, $longitud);        
        $I->amOnPage('/intranet/admin');
        $I->click('Recambios', '.list-group-item');
        $I->seeCurrentUrlEquals('/intranet/buys/supplies/list');
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
        $this->id = $I->grabFromDatabase('supplies', 'id', array('ref' => $referencia));
     
    }
    public function UpdateSupplyTest(FunctionalTester $I)
    {
        $I->amOnPage('/intranet/admin');
        $I->click('Recambios', '.list-group-item');
        $I->seeCurrentUrlEquals('/intranet/buys/supplies/list');
        $I->wantTo('Update Supply');
        $I->amOnPage('/intranet/buys/supplies/form?id='.$this->id);
        $I->click('#submit');
            
    }
    public function DeleteSupplyTest(FunctionalTester $I)
    {
        $I->amOnPage('/intranet/admin');
        $I->click('Recambios', '.list-group-item');
        $I->seeCurrentUrlEquals('/intranet/buys/supplies/list');
        $I->wantTo('Delete Supply');
        $I->amOnPage('/intranet/buys/supplies/delete?id='.$this->id);
    
    }
}
