<?php

class SuppliesActionsCest
{
    protected $name, $id, $permitted_chars, $ref, $serial;
    
    public function _before(FunctionalTester $I) {
        $I->amOnPage("/");
        $I->submitForm('#loginForm', ['email' => 'tonyllomouse@gmail.com', 'password' => '12345']);
    }

    // tests
    public function addSupplyTest(FunctionalTester $I) {
        $I->amOnPage("/vehicles/supplies/list?menu=stock&item=supplies");
        $I->click('#newButton');
        $this->permitted_chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $this->name = substr(str_shuffle($this->permitted_chars), 0, 8);   
        $this->ref = substr(str_shuffle($this->permitted_chars), 0, 10);    
        $this->serial = substr(str_shuffle($this->permitted_chars), 0, 20);    
        $maders = $I->grabColumnFromDatabase('maders', 'id', ['deleted_at' => null]);
        $supply = ['name' => $this->name, 'ref' => $this->ref, 'serialNumber' => $this->serial, 'mader_id' => $maders[count($maders) - 1], 'pvc' => 10, 'pvp' => 20];
        $I->submitForm('#formRecambio', $supply);        
        $this->id = $I->grabFromDatabase('supplies', 'id', ['name' => $this->name]);        
        $I->seeInDatabase('supplies', ['name' => $this->name]);
    }

    public function editSupplyTest(FunctionalTester $I) {
        $I->amOnPage("/vehicles/supplies/list?menu=stock&item=supplies");
        $I->click('#editButton' . $this->id);        
        $this->ref = substr(str_shuffle($this->permitted_chars), 0, 10); 
        $supply = ['name' => $this->name, 'ref' => $this->ref];
        $I->submitForm('#formRecambio', $supply);  
        $I->see('Updated');
    }

    public function delFromSupplysListTest(FunctionalTester $I) {
        $I->amOnPage("/vehicles/supplies/list?menu=stock&item=supplies");
        $I->click('#delButton' . $this->id);
        $I->dontSeeInDatabase('supplies', array('id' => intval($this->id), 'deleted_at' => null));
    }

    public function delFromSupplyFormTest(FunctionalTester $I) {
        $this->addSupplyTest($I);
        $this->_before($I);
        $I->amOnPage("/vehicles/supplies/list?menu=stock&item=supplies");        
        $I->click('#editButton' . $this->id);
        $I->click('Eliminar');
        $I->dontSeeInDatabase('supplies', array('id' => intval($this->id), 'deleted_at' => null));
    }
}
