<?php

namespace Tests\acceptance;

use AcceptanceTester;
use Tests\acceptance\FirstCest;


class TaxesCest
{
    protected $id;
    public function _before(AcceptanceTester $I){
        FirstCest::loginTest($I);
    }
    public function accesTest(AcceptanceTester $I)
    {        
        $I->amOnPage('/Intranet/');
        $I->amOnPage('/Intranet/admin');
        $I->click('Tipos Iva', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/taxes/list');
    }
    public function saveTaxTest(AcceptanceTester $I){
        $I->amOnPage('/Intranet/admin');
        $I->click('Tipos Iva', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/taxes/list');
        $caracteres_permitidos = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';        
        $longitud = 12;
        $name = substr(str_shuffle($caracteres_permitidos), 0, $longitud);
        $I->wantTo('Create a new Tax');
        $I->click('#submit', '#addTaxes');
        $I->seeCurrentUrlEquals('/Intranet/taxes/form'); 
        $I->submitForm('#taxesForm', array ('name' => $name, 'percentaje' => 21));
        $this->id = $I->grabFromDatabase('taxes', 'id', array('name' => $name));
        $I->see('Saved');       
    }
    public function updateTaxTest(AcceptanceTester $I){
        $I->amOnPage('/Intranet/admin');
        $I->click('Tipos Iva', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/taxes/list');
        $caracteres_permitidos = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';        
        $longitud = 12;
        $name = substr(str_shuffle($caracteres_permitidos), 0, $longitud);
        $I->wantTo('Update Tax');
        $I->amOnPage('/Intranet/taxes/form?id='.$this->id);
        $I->submitForm('#taxesForm', array ('name' => $name, 'percentaje' => 21));
        $I->see('Updated'); 
    }
}
