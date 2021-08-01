<?php 

namespace App\Tests;

use FunctionalTester;
use homeCest;

class companyCest
{
    protected $id;
    
    public function _before(FunctionalTester $I)
    {
         homeCest::loginTest($I);
    }
//    tests
    public function SaveCompanyTest(FunctionalTester $I){
        $caracteres_permitidos = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $longitud = 12;
        $fiscal_id = substr(str_shuffle($caracteres_permitidos), 0, $longitud);
        $I->amOnPage('/intranet/admin');
        $I->click('Empresas', '.list-group-item');
        $I->seeCurrentUrlEquals('/intranet/company/list');
        $I->wantTo('Create a new Company');
        $I->click('#submit', '#addCompany');
        $I->seeCurrentUrlEquals('/intranet/company/form');        
        $I->submitForm('#CompanyForm', 
                array ('name' => 'Lorem Ipsum S.L.', 
                    'fiscal_id' => $fiscal_id, 
                    'fiscal_name' => 'Lorem Ipsum S.L.',
                    'address' => 'Avenida Lorem Ipsum, 5',
                    'city' => 'LOREM',
                    'postal_code' => '12345',
                    'state' => 'IPSUM',
                    'country' => 'LOREMIPSUM',
                    'phone' => '1234-4546565',
                    'email' => 'loremipsum@loremipsum.com',
                    'site' => 'loremipsum.com' 
                    )); 
        $this->id = $I->grabFromDatabase('company', 'id', array('fiscalId' => $fiscal_id));      
        $I->canSeeCurrentUrlEquals('/intranet/company/save');
            
    }
    public function UpdateCompanyTest(FunctionalTester $I){
        $I->amOnPage('/intranet/admin');
        $I->click('Empresas', '.list-group-item');
        $I->seeCurrentUrlEquals('/intranet/company/list');
        $I->wantTo('Update Company');
        $I->amOnPage('/intranet/company/form?id='.$this->id);
        $I->see('Empresas');
        $I->submitForm('#CompanyForm', 
                array ('postal_code' => '54321'                    
                    ));        
        
    }
    public function DeleteCompanyTest(FunctionalTester $I){
        $I->amOnPage('/intranet/admin');
        $I->click('Empresas', '.list-group-item');
        $I->seeCurrentUrlEquals('/intranet/company/list');
        $I->amOnPage('/intranet/company/delete?id='.$this->id);        
    }
            
}
