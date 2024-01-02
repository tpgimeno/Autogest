<?php

class ComponentsActionsCest
{
    protected $name, $id, $permitted_chars, $ref, $serial;
    
    public function _before(FunctionalTester $I) {
        $I->amOnPage("/");
        $I->submitForm('#loginForm', ['email' => 'tonyllomouse@gmail.com', 'password' => '12345']);
    }

    // tests
    public function addComponentTest(FunctionalTester $I) {
        $I->amOnPage("/vehicles/components/list?menu=stock&item=components");
        $I->click('#newButton');
        $this->permitted_chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $this->name = substr(str_shuffle($this->permitted_chars), 0, 8);   
        $this->ref = substr(str_shuffle($this->permitted_chars), 0, 10);    
        $this->serial = substr(str_shuffle($this->permitted_chars), 0, 20);    
        $maders = $I->grabColumnFromDatabase('maders', 'id', ['deleted_at' => null]);
        $component = ['name' => $this->name, 'ref' => $this->ref, 'serialNumber' => $this->serial, 'mader_id' => $maders[count($maders) - 1], 'pvc' => 10, 'pvp' => 20];
        $I->submitForm('#formComponente', $component);        
        $this->id = $I->grabFromDatabase('components', 'id', ['name' => $this->name]);        
        $I->seeInDatabase('components', ['name' => $this->name]);
    }

    public function editComponentTest(FunctionalTester $I) {
        $I->amOnPage("/vehicles/components/list?menu=stock&item=components");
        $I->click('#editButton' . $this->id);        
        $this->ref = substr(str_shuffle($this->permitted_chars), 0, 10); 
        $component = ['name' => $this->name, 'ref' => $this->ref];
        $I->submitForm('#formComponente', $component);  
        $I->see('Updated');
    }

    public function delFromComponentsListTest(FunctionalTester $I) {
        $I->amOnPage("/vehicles/components/list?menu=stock&item=components");
        $I->click('#delButton' . $this->id);
        $I->dontSeeInDatabase('components', array('id' => intval($this->id), 'deleted_at' => null));
    }

    public function delFromComponentFormTest(FunctionalTester $I) {
        $this->addComponentTest($I);
        $this->_before($I);
        $I->amOnPage("/vehicles/components/list?menu=stock&item=components");        
        $I->click('#editButton' . $this->id);
        $I->click('Eliminar');
        $I->dontSeeInDatabase('components', array('id' => intval($this->id), 'deleted_at' => null));
    }
}
