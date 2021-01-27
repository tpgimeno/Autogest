<?php 

class SellOffersCest
{
    protected $id;
    protected $customerId;
    protected $vehicleId;
    
    public function _before(FunctionalTester $I)
    {
        homeCest::loginTest($I);
    }
    // tests
    public function SaveSellOfferTest(FunctionalTester $I)
    {
        $caracteres_permitidos = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $longitud = 7;        
        $fiscal_id = substr(str_shuffle($caracteres_permitidos), 0, $longitud);        
        $I->amOnPage('/intranet/admin');
        $I->click('Ofertas', '.list-group-item');
        $I->seeCurrentUrlEquals('/intranet/sells/offers/list');
        $I->wantTo('Create a new SellOffer');
        $I->click('#submit', '#addSellOffer');
        $I->click('#searchCustomer');
        $I->canSee('Clientes');
        $I->click('#select', Locator::lastElement('//table/tr'));           
        $this->id = $I->grabFromDatabase('customers', 'id', array('fiscal_id' => $fiscal_id));
        $I->canSee('Saved');
    }
    public function UpdateSellOfferTest(FunctionalTester $I)
    {
        $I->amOnPage('/intranet/admin');
        $I->click('Clientes', '.list-group-item');
        $I->seeCurrentUrlEquals('/intranet/customers/list');
        $I->wantTo('Update Customer');
        $I->amOnPage('/intranet/customers/form?id='.$this->id);
        $I->click('#submit');
        $I->canSee('Updated');     
    }
    public function DeleteSellOfferTest(FunctionalTester $I)
    {
        $I->amOnPage('/intranet/admin');
        $I->click('Clientes', '.list-group-item');
        $I->seeCurrentUrlEquals('/intranet/customers/list');
        $I->wantTo('Delete Customer');
        $I->amOnPage('/intranet/customers/delete?id='.$this->id);
        $I->canSee('Clientes');
    }
}
