<?php
namespace Tests\acceptance;

use AcceptanceTester;
use Tests\acceptance\FirstCest;
class MadersCest
{
    protected $id;
    protected $fiscalId;
    protected $site;
    public function _before(AcceptanceTester $I) {
        FirstCest::loginTest($I);
    }
    // tests
    public function accesMader(AcceptanceTester $I) {
        $I->wantTo('Acces Maders Pages');
        $I->amOnPage('/Intranet/');
        $I->amOnPage('/Intranet/admin');
        $I->click('Fabricantes', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/maders/list');
        $I->click('#submit', '#addMader');
        $I->seeCurrentUrlEquals('/Intranet/maders/form');        
    }
    public function saveMaderTest(AcceptanceTester $I){
        $I->amOnPage('/Intranet/admin');
        $I->click('Fabricantes', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/maders/list');
        $caracteres_permitidos = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';        
        $longitud = 10; 
        $this->site = substr(str_shuffle($caracteres_permitidos), 0, $longitud).".com";
        $email = substr(str_shuffle($caracteres_permitidos), 0, $longitud)."@".$this->site;
        $this->fiscalId = substr(str_shuffle($caracteres_permitidos), 0, $longitud);        
        $I->wantTo('Create a new Mader');
        $I->click('#submit', '#addMader');
        $I->seeCurrentUrlEquals('/Intranet/maders/form'); 
        $I->submitForm('#madersForm', array ('fiscalId' => $this->fiscalId,
            'name' => 'Lorem',           
            'address' => 'Lorem ipsum, 25',
            'city' => 'Ipsum', 
            'postalCode' => 15874, 
            'state' => 'Lorem', 
            'country' => 'LoremIpsum',
            'phone' => 95874581,
            'email' => $email,
            'site' => $this->site));
        $this->id = $I->grabFromDatabase('maders', 'id', array('fiscalId' => $this->fiscalId));
        $I->see('Saved');       
    }
    public function updateMaderTest(AcceptanceTester $I){
        $I->amOnPage('/Intranet/admin');
        $I->click('Fabricantes', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/maders/list');
        $caracteres_permitidos = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';        
        $longitud = 10;
        $email = substr(str_shuffle($caracteres_permitidos), 0, $longitud)."@".$this->site;
        $I->wantTo('Update Mader');
        $I->amOnPage('/Intranet/maders/form?id='.$this->id);
        $I->submitForm('#madersForm', array ('fiscalId' => $this->fiscalId,
            'name' => 'Lorem',           
            'address' => 'Lorem ipsum, 25',
            'city' => 'Ipsum', 
            'postalCode' => 15874, 
            'state' => 'Lorem', 
            'country' => 'LoremIpsum',
            'phone' => 95874581,
            'email' => $email,
            'site' => $this->site));
        $I->see('Updated'); 
    }
    public function deleteMaderTest(AcceptanceTester $I){
        $I->wantTo('Delete Mader');
        $I->amOnPage('/Intranet/admin');
        $I->click('Fabricantes', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/maders/list');
        $I->amOnPage('/Intranet/maders/delete?id='.$this->id); 
        $I->dontSeeInDatabase('maders', array('id' => intval($this->id), 'deleted_at' => null));
    }
}
