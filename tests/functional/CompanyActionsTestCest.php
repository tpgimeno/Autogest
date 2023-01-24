<?php

namespace Tests\functional;

use FunctionalTester;

class CompanyActionsTestCest {

    public $permitted_chars;
    protected $fiscalId;
    protected $id;
    
    public function _before(FunctionalTester $I) {
        $I->amOnPage("/");
        $I->submitForm('#loginForm', ['email' => 'tonyllomouse@gmail.com', 'password' => '12345']);
    }

    // tests
    public function addCompanyTest(FunctionalTester $I) {
        $I->amOnPage("/company/list?menu=mantenimiento&item=companies");
        $I->click('#newButton');
        $this->permitted_chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $this->fiscalId = substr(str_shuffle($this->permitted_chars), 0, 10);
        $empresa = ['name' => 'LoremIpsum', 'fiscalId' => $this->fiscalId, 'fiscalName' => 'LoremIpsum SA', 'address' => 'C/ Lorem Ipsum, 123', 'postalCode' => random_int(10000, 99999), 'city' => 'Ipsum', 'state' => 'Lorem', 'country' => 'IpsumLorem', 'phone' => '962541144', 'email' => 'loremipsum@loremipsum.com', 'site' => 'www.loremipsum.com'];
        $I->submitForm('#formEmpresa', $empresa);
        $this->id = $I->grabFromDatabase('company', 'id', ['fiscalId' => $this->fiscalId]);        
        $I->see('Saved');
    }

    public function editCompanyTest(FunctionalTester $I) {
        $I->amOnPage("/company/list?menu=mantenimiento&item=companies");        
        $I->click('#editButton' . $this->id);
        $empresa = ['id' => $this->id, 'name' => 'LoremIpsumEdited', 'fiscalId' => substr(str_shuffle($this->permitted_chars), 0, 10), 'fiscalName' => 'LoremIpsum SA', 'address' => 'C/ Lorem Ipsum, 123', 'postalCode' => random_int(10000, 99999), 'city' => 'Ipsum', 'state' => 'Lorem', 'country' => 'IpsumLorem', 'phone' => '962541144', 'email' => 'loremipsum@loremipsum.com', 'site' => 'www.loremipsum.com'];
        $I->submitForm('#formEmpresa', $empresa);
        $I->see('Updated');        
    }
    
    public function delFromCompanyListTest(FunctionalTester $I) {
        $I->amOnPage("/company/list?menu=mantenimiento&item=companies");
        $I->click('#delButton' . $this->id);
        $I->dontSeeInDatabase('company', array('id' => intval($this->id), 'deleted_at' => null));
        
    }
    
    public function delFromCompanyFormTest(FunctionalTester $I) {
        $I->amOnPage("/company/list?menu=mantenimiento&item=companies");
        $lastRegister = $I->grabNumRecords('company', array('deleted_at' => null)); 
        if($lastRegister === 0){
            $this->addCompanyTest($I);
            $lastRegister = $I->grabNumRecords('company', array('deleted_at' => null));
        }
        $registers = $I->grabColumnFromDatabase('company', 'id', array('deleted_at' => null));        
        $I->click('#editButton' . $registers[$lastRegister -1]);
        $I->click('Eliminar');
        $I->dontSeeInDatabase('company', array('id' => intval($lastRegister), 'deleted_at' => null));
    }
    

}
