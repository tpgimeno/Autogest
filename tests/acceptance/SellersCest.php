<?php
namespace Tests\acceptance;

use AcceptanceTester;
use Tests\acceptance\FirstCest;
class SellersCest
{
    protected $id;
    protected $fiscalId;   
    public function _before(AcceptanceTester $I) {
        FirstCest::loginTest($I);
    }
    // tests
    public function accesSeller(AcceptanceTester $I) {
        $I->wantTo('Acces Sellers Pages');
        $I->amOnPage('/Intranet/');
        $I->amOnPage('/Intranet/admin');
        $I->click('Comerciales', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/sellers/list');
        $I->click('#submit', '#addSeller');
        $I->seeCurrentUrlEquals('/Intranet/sellers/form');        
    }
    public function saveSellerTest(AcceptanceTester $I){
        $I->amOnPage('/Intranet/admin');
        $I->click('Comerciales', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/sellers/list');
        $caracteres_permitidos = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';        
        $longitud = 10;         
        $email = substr(str_shuffle($caracteres_permitidos), 0, $longitud)."@".substr(str_shuffle($caracteres_permitidos), 0, $longitud).".com";
        $this->fiscalId = substr(str_shuffle($caracteres_permitidos), 0, $longitud);        
        $I->wantTo('Create a new Seller');
        $I->click('#submit', '#addSeller');
        $I->seeCurrentUrlEquals('/Intranet/sellers/form'); 
        $I->submitForm('#sellersForm', array ('name' => 'Lorem', 
            'fiscalId' => $this->fiscalId,                      
            'address' => 'Lorem ipsum, 25',
            'city' => 'Ipsum', 
            'postalCode' => 15874, 
            'state' => 'Lorem', 
            'country' => 'LoremIpsum',
            'phone' => 95874581,
            'email' => $email,
            'birthDate' => '12/10/1978'));
        $this->id = $I->grabFromDatabase('sellers', 'id', array('fiscalId' => $this->fiscalId));
        $I->see('Saved');       
    }
    public function updateSellerTest(AcceptanceTester $I){
        $I->amOnPage('/Intranet/admin');
        $I->click('Comerciales', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/sellers/list');
        $caracteres_permitidos = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';        
        $longitud = 10;
        $email = substr(str_shuffle($caracteres_permitidos), 0, $longitud)."@".substr(str_shuffle($caracteres_permitidos), 0, $longitud).".com";
        $I->wantTo('Update Seller');
        $I->amOnPage('/Intranet/sellers/form?id='.$this->id);
        $I->submitForm('#sellersForm', array ('id' => $this->id,
            'name' => 'Lorem', 
            'fiscalId' => $this->fiscalId,                      
            'address' => 'Lorem ipsum, 25',
            'city' => 'Ipsum', 
            'postalCode' => 15874, 
            'state' => 'Lorem', 
            'country' => 'LoremIpsum',
            'phone' => 95874581,
            'email' => $email,
            'birthDate' => '12/10/1978'));
        $I->see('Updated'); 
    }
    public function deleteSellerTest(AcceptanceTester $I){
        $I->wantTo('Delete Seller');
        $I->amOnPage('/Intranet/admin');
        $I->click('Comerciales', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/sellers/list');
        $I->amOnPage('/Intranet/sellers/delete?id='.$this->id); 
        $I->dontSeeInDatabase('sellers', array('id' => intval($this->id), 'deleted_at' => null));
    }
}
