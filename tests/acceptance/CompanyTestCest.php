<?php

class CompanyTestCest {

    protected $id;

    public function _before(AcceptanceTester $I) {
        $I->amOnPage("/");
        $I->see("Iniciar sesión");
        $I->submitForm('#loginForm', ['email' => 'tonyllomouse@gmail.com', 'password' => '12345']);
        $I->amGoingTo("Valido la página en que me encuentro");
        $I->see("Dashboard");
    }

    // tests
    public function accessCompaniesList(AcceptanceTester $I) {
        $I->amOnPage("/company/list?menu=mantenimiento&item=companies");
        $I->see('Empresas');
    }

    public function newCompanyTest(AcceptanceTester $I) {
        $I->amOnPage("/company/list?menu=mantenimiento&item=companies");
        $I->click('#newButton');
        $I->see('Empresa');
    }

    public function addCompanyTest(AcceptanceTester $I) {
        $I->amOnPage("/company/list?menu=mantenimiento&item=companies");
        $I->click('#newButton');
        $permitted_chars = '0123456789ABCDEFGHIJKLMNÑOPQRSTUVWXYZ';
        $fiscalId = substr(str_shuffle($permitted_chars), 0, 10);
        $I->submitForm('#formEmpresa', array('name' => 'LoremIpsum',
            'fiscalId' => $fiscalId,
            'fiscalName' => 'LoremIpsum SA',
            'address' => 'C/ Lorem Ipsum, 123',
            'postalCode' => random_int(10000, 99999),
            'city' => 'Ipsum', 
            'state' => 'Lorem',
            'country' => 'IpsumLorem',
            'phone' => '962541144',
            'email' => 'loremipsum@loremipsum.com',
            'site' => 'www.loremipsum.com'));
        $this->id = $I->grabFromDatabase('company', 'id', ['name' => 'LoremIpsum']);
        $I->see('Saved');
    }

    public function editCompanyTest(AcceptanceTester $I) {
        $I->amOnPage("/company/list?menu=mantenimiento&item=companies");
        $I->click('#editButton' . $this->id);
        $I->see('Empresa');
    }

}
