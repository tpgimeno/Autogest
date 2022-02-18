<?php

namespace Tests\acceptance;

use AcceptanceTester;
use Tests\acceptance\FirstCest;

class UsersCest
{
    protected $id;
    public function _before(AcceptanceTester $I) {
       FirstCest::loginTest($I); 
    }

    // tests
    public function accesUser(AcceptanceTester $I)
    {
        $I->amOnPage('/Intranet/');
        $I->amOnPage('/Intranet/admin');
        $I->click('Usuarios', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/users/list');
    }
    public function saveUserTest(AcceptanceTester $I){
        $I->amOnPage('/Intranet/admin');
        $I->click('Usuarios', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/users/list');
        $caracteres_permitidos = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';        
        $longitud = 10;
        $email = substr(str_shuffle($caracteres_permitidos), 0, $longitud)."@".substr(str_shuffle($caracteres_permitidos), 0, $longitud).".com";
        $I->wantTo('Create a new User');
        $I->click('#submit', '#addUser');
        $I->seeCurrentUrlEquals('/Intranet/users/form'); 
        $I->submitForm('#userForm', array ('email' => $email, 'password' => substr(str_shuffle($caracteres_permitidos), 0, $longitud)));
        $this->id = $I->grabFromDatabase('users', 'id', array('email' => $email));
        $I->see('Saved');       
    }
    public function updateUserTest(AcceptanceTester $I){
        $I->amOnPage('/Intranet/admin');
        $I->click('Usuarios', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/users/list');
        $caracteres_permitidos = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';        
        $longitud = 10;
        $email = substr(str_shuffle($caracteres_permitidos), 0, $longitud)."@".substr(str_shuffle($caracteres_permitidos), 0, $longitud).".com";
        $I->wantTo('Update User');
        $I->amOnPage('/Intranet/users/form?id='.$this->id);
        $I->submitForm('#userForm', array ('email' => $email));
        $I->see('Updated'); 
    }
     public function deleteUserTest(AcceptanceTester $I){
        $I->wantTo('Delete User');
        $I->amOnPage('/Intranet/admin');
        $I->click('Usuarios', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/users/list');
        $I->amOnPage('/Intranet/users/delete?id='.$this->id); 
        $I->dontSeeInDatabase('users', array('id' => intval($this->id), 'deleted_at' => null));
    }
}
