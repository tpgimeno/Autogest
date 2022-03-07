<?php

namespace Tests\acceptance;

use AcceptanceTester;
use Tests\acceptance\FirstCest;

class PaymentWaysCest
{
    protected $id;   
    protected $name;
    protected $account;
    protected $accountId;
    public function _before(AcceptanceTester $I) {
        FirstCest::loginTest($I);
    }
    public function accesPaymentWay(AcceptanceTester $I) {
        $I->wantTo('Acces PaymentWay Pages');
        $I->amOnPage('/Intranet/');
        $I->amOnPage('/Intranet/admin');
        $I->click('Formas de Pago', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/paymentWays/list');        
        $I->click('#submit', '#addPaymentWay');
        $I->seeCurrentUrlEquals('/Intranet/paymentWays/form'); 
    }
    public function savePaymentWayTest(AcceptanceTester $I){
        $I->amOnPage('/Intranet/admin');
        $I->click('Formas de Pago', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/paymentWays/list');
        $caracteres_permitidos = 'abcdefghijklmnñopqrstuvwxyzABCDEFGHIJKLMNÑOPQRSTUVWXYZ';        
        $longitud = 8;        
        $this->name = substr(str_shuffle($caracteres_permitidos), 0, $longitud);        
        $I->wantTo('Create a new PaymentWay');
        $I->click('#submit', '#addPaymentWay');
        $I->seeCurrentUrlEquals('/Intranet/paymentWays/form'); 
        $this->account = $I->grabFromDatabase('accounts', 'accountNumber', array('id' => 1));
        $this->accountId = $I->grabFromDatabase('accounts', 'id', array('accountNumber' => $this->account));
        $I->submitForm('#paymentWaysForm', array('name' => $this->name, 'account' => $this->accountId, 'discount' => 10));        
        $this->id = $I->grabFromDatabase('paymentWays', 'id', array('name' => $this->name));
        $I->see('Saved');       
    }
    public function updatePaymentWayTest(AcceptanceTester $I){
        $I->amOnPage('/Intranet/admin');
        $I->click('Formas de Pago', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/paymentWays/list');       
        $I->wantTo('Update PaymentWay');
        $I->amOnPage('/Intranet/paymentWays/form?id='.$this->id);
        $caracteres_permitidos = 'abcdefghijklmnñopqrstuvwxyzABCDEFGHIJKLMNÑOPQRSTUVWXYZ';
        $longitud = 8;        
        $this->name = substr(str_shuffle($caracteres_permitidos), 0, $longitud); 
        $I->submitForm('#paymentWaysForm', array('id' => $this->id, 'name' => $this->name, 'account' => $this->accountId, 'discount' => 10)); 
        $I->see('Updated'); 
    }
     public function deletePaymentWayTest(AcceptanceTester $I){
        $I->wantTo('Delete PaymentWay');
        $I->amOnPage('/Intranet/admin');
        $I->click('Formas de Pago', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/paymentWays/list');
        $I->amOnPage('/Intranet/paymentWays/delete?id='.$this->id); 
        $I->dontSeeInDatabase('paymentWays', array('id' => intval($this->id), 'deleted_at' => null));
    }
}
