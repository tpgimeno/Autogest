<?php 

namespace App\Tests;

use FunctionalTester;


class companyCest
{
    protected $id;  

//    tests
    public function SaveCompanyTest(FunctionalTester $I){
        $caracteres_permitidos = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';        
        $longitud = 12;
        $fiscal_id = substr(str_shuffle($caracteres_permitidos), 0, $longitud);
        $I->amOnPage('/Intranet/admin');
        $I->click('Empresas', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/company/list');
        $I->wantTo('Create a new Company');
        $I->click('#submit', '#addCompany');
        $I->seeCurrentUrlEquals('/Intranet/company/form');        
        $I->submitForm('#CompanyForm', array ('name' => 'Lorem Ipsum S.L.', 'fiscalId' => $fiscal_id, 'fiscalName' => 'Lorem Ipsum S.L.', 'address' => 'Avenida Lorem Ipsum, 5', 'city' => 'LOREM', 'postalCode' => '12345', 'state' => 'IPSUM', 'country' => 'LOREMIPSUM', 'phone' => '1234-4546565', 'email' => 'loremipsum@loremipsum.com', 'site' => 'loremipsum.com')); 
        $this->id = $I->grabFromDatabase('company', 'id', array('fiscalId' => $fiscal_id));      
        $I->canSeeCurrentUrlEquals('/Intranet/company/save');
        $I->canSee('Saved');    
    }
    public function UpdateCompanyTest(FunctionalTester $I){
        $I->amOnPage('/Intranet/admin');
        $I->click('Empresas', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/company/list');
        $I->wantTo('Update Company');
        $I->amOnPage('/Intranet/company/form?id='.$this->id);
        $I->see('Empresas');
        $I->click('#submit');       
        $I->canSee('Updated');
    }
    public function DeleteCompanyTest(FunctionalTester $I){
        $I->amOnPage('/Intranet/admin');
        $I->click('Empresas', '.list-group-item');
        $I->seeCurrentUrlEquals('/Intranet/company/list');
        $I->amOnPage('/Intranet/company/delete?id='.$this->id);        
    }
            
}
