<?php

class AssurancesActionsCest
{
    public $permitted_chars;
    protected $id, $ref;
    
    public function _before(FunctionalTester $I) {
        $I->amOnPage("/");
        $I->submitForm('#loginForm', ['email' => 'tonyllomouse@gmail.com', 'password' => '12345']);
    }

    // tests
    public function addAssuranceTest(FunctionalTester $I) {
        $I->amOnPage("/assurances/list?menu=mantenimiento&item=assurances");
        $I->click('#newButton');
        $this->permitted_chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $assuranceRefs = $I->grabColumnFromDatabase('assurances', 'ref', ['deleted_at' => null]);
        $this->ref = substr(str_shuffle($this->permitted_chars), 0, 20);
        while(array_search($this->ref, $assuranceRefs)){
            $this->ref = substr(str_shuffle($this->permitted_chars), 0, 20);
        } 
        $customers = $I->grabColumnFromDatabase('customers', 'id', ['deleted_at' => null]);
        $assurance = ['ref' => $this->ref, 'inDate' => '12/12/2023 12:00:00','effectDate' => '12/12/2023 12:00:00', 'getter_id' => $customers[count($customers) - 1],'owner_id' => $customers[count($customers) - 1], 'description' => 'Lorem ipsum ...', 'description' => 'Lorem ipsum ...'];
        $I->submitForm('#formPolizadeSeguro', $assurance);        
        $this->id = $I->grabFromDatabase('assurances', 'id', ['ref' => $this->ref]);        
        $I->seeInDatabase('assurances', ['ref' => $this->ref]);
    }

    public function editAssuranceTest(FunctionalTester $I) {
        $I->amOnPage("/assurances/list?menu=mantenimiento&item=assurances");
        $I->click('#editButton' . $this->id);
        $this->ref = substr(str_shuffle($this->permitted_chars), 0, 20);
        $assuranceRefs = $I->grabColumnFromDatabase('assurances', 'ref', ['deleted_at' => null]);
        while(array_search($this->ref, $assuranceRefs)){
            $this->ref = substr(str_shuffle($this->permitted_chars), 0, 20);
        }         
        $assurance = ['ref' => $this->ref];
        $I->submitForm('#formPolizadeSeguro', $assurance); 
        $I->see('Updated');
    }

    public function delFromAssurancesListTest(FunctionalTester $I) {
        $I->amOnPage("/assurances/list?menu=mantenimiento&item=assurances");
        $I->click('#delButton' . $this->id);
        $I->dontSeeInDatabase('assurances', array('id' => intval($this->id), 'deleted_at' => null));
    }

    public function delFromAssuranceFormTest(FunctionalTester $I) {
        $this->addAssuranceTest($I);
        $this->_before($I);
        $I->amOnPage("/assurances/list?menu=mantenimiento&item=assurances");        
        $I->click('#editButton' . $this->id);
        $I->click('Eliminar');
        $I->dontSeeInDatabase('assurances', array('id' => intval($this->id), 'deleted_at' => null));
    }
}
