<?php
namespace Tests\acceptance;

use AcceptanceTester;
use Tests\acceptance\FirstCest;
class GaragesCest
{
    protected $id;
    protected $fiscalId;
    public function _before(AcceptanceTester $I) {
        FirstCest::loginTest($I);
    }
    // tests
    public function accesGarage(AcceptanceTester $I) {
        $I->wantTo('Acces Garages Pages');
        $I->amOnPage('/Intranet/');
        $I->amOnPage('/Intranet/admin');
        $I->click('Talleres', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/garages/list');
        $I->click('#submit', '#addGarage');
        $I->seeCurrentUrlEquals('/Intranet/garages/form');        
    }
    public function saveGarageTest(AcceptanceTester $I){
        $I->amOnPage('/Intranet/admin');
        $I->click('Talleres', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/garages/list');
        $caracteres_permitidos = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';        
        $longitud = 10;       
        $email = substr(str_shuffle($caracteres_permitidos), 0, $longitud)."@".substr(str_shuffle($caracteres_permitidos), 0, $longitud).".com";
        $this->fiscalId = substr(str_shuffle($caracteres_permitidos), 0, $longitud);        
        $I->wantTo('Create a new Garage');
        $I->click('#submit', '#addGarage');
        $I->seeCurrentUrlEquals('/Intranet/garages/form'); 
        $I->submitForm('#garagesForm', array ('name' => 'Lorem',
            'fiscalId' => $this->fiscalId,
            'fiscalName' => 'LoremIpsum',
            'address' => 'Lorem ipsum, 25',
            'city' => 'Ipsum', 
            'postalCode' => 15874, 
            'state' => 'Lorem', 
            'country' => 'LoremIpsum',
            'phone' => 95874581,
            'email' => $email));
        $this->id = $I->grabFromDatabase('garages', 'id', array('fiscalId' => $this->fiscalId));
        $I->see('Saved');       
    }
    public function updateGarageTest(AcceptanceTester $I){
        $I->amOnPage('/Intranet/admin');
        $I->click('Talleres', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/garages/list');
        $caracteres_permitidos = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';        
        $longitud = 10;
        $email = substr(str_shuffle($caracteres_permitidos), 0, $longitud)."@".substr(str_shuffle($caracteres_permitidos), 0, $longitud).".com";
        $I->wantTo('Update Garage');
        $I->amOnPage('/Intranet/garages/form?id='.$this->id);
        $I->submitForm('#garagesForm', array ('id' => $this->id,
            'name' => 'Lorem',
            'fiscalId' => $this->fiscalId,
            'fiscalName' => 'LoremIpsum',
            'address' => 'Lorem ipsum, 25',
            'city' => 'Ipsum', 
            'postalCode' => 15874, 
            'state' => 'Lorem', 
            'country' => 'LoremIpsum',
            'phone' => 95874581,
            'email' => $email));
        $I->see('Updated'); 
    }
    public function deleteGarageTest(AcceptanceTester $I){
        $I->wantTo('Delete Garage');
        $I->amOnPage('/Intranet/admin');
        $I->click('Talleres', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/garages/list');
        $I->amOnPage('/Intranet/garages/delete?id='.$this->id); 
        $I->dontSeeInDatabase('garages', array('id' => intval($this->id), 'deleted_at' => null));
    }
}
