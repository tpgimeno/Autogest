<?php

class ModelsActionsCest
{
    protected $name, $id, $permitted_chars;
    
    public function _before(FunctionalTester $I)  {
        $I->amOnPage("/");
        $I->submitForm('#loginForm', ['email' => 'tonyllomouse@gmail.com', 'password' => '12345']);
    }

    // tests
    public function addModelTest(FunctionalTester $I) {
        $I->amOnPage("/vehicles/models/list?menu=stock&item=models");
        $I->click('#newButton');
        $this->permitted_chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $this->name = substr(str_shuffle($this->permitted_chars), 0, 12); 
        $brands = $I->grabColumnFromDatabase('brands', 'id', ['deleted_at' => null]);
        $model = ['brand_id' => $brands[count($brands) - 1], 'name' => $this->name];
        $I->submitForm('#formModelo', $model);        
        $this->id = $I->grabFromDatabase('models', 'id', ['name' => $this->name]);        
        $I->seeInDatabase('models', ['name' => $this->name]);
    }

    public function editModelTest(FunctionalTester $I) {
        $I->amOnPage("/vehicles/models/list?menu=stock&item=models");
        $I->click('#editButton' . $this->id);        
        $this->name = substr(str_shuffle($this->permitted_chars), 0, 12); 
        $model = ['name' => $this->name];
        $I->submitForm('#formModelo', $model);
        $I->see('Updated');
    }

    public function delFromModelsListTest(FunctionalTester $I) {
        $I->amOnPage("/vehicles/models/list?menu=stock&item=models");
        $I->click('#delButton' . $this->id);
        $I->dontSeeInDatabase('models', array('id' => intval($this->id), 'deleted_at' => null));
    }

    public function delFromModelFormTest(FunctionalTester $I) {
        $this->addModelTest($I);
        $this->_before($I);
        $I->amOnPage("/vehicles/models/list?menu=stock&item=models");        
        $I->click('#editButton' . $this->id);
        $I->click('Eliminar');
        $I->dontSeeInDatabase('models', array('id' => intval($this->id), 'deleted_at' => null));
    } 
}
