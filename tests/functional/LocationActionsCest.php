<?php

class LocationActionsCest {

    public $permitted_chars;
    protected $id;
    protected $name;
    

    public function _before(FunctionalTester $I) {
        $I->amOnPage("/");
        $I->submitForm('#loginForm', ['email' => 'tonyllomouse@gmail.com', 'password' => '12345']);        
    }

    // tests
    public function addLocationTest(FunctionalTester $I) {
        $I->amOnPage("/locations/list?menu=stock&item=locations");
        $I->click('#newButton'); 
        $numStores = $I->grabNumRecords('stores');
        $stores = $I->grabColumnFromDatabase('stores', 'id');
        $lastStore = 0;
        for($i = 1; $i < $numStores; $i++){
            if($lastStore < $stores[$i]){
                $lastStore = $stores[$i];
            }
        }               
        $this->permitted_chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $this->name = substr(str_shuffle($this->permitted_chars), 0, 2);
        $location = [ 'store_id' => $lastStore, 'name' => $this->name];
        $I->submitForm('#formUbicacion', $location);
        $this->id = $I->grabFromDatabase('locations', 'id', ['store_id' => $lastStore, 'name' => $this->name]);
        $I->see('Saved');
    }

    public function editLocationTest(FunctionalTester $I) {
        $I->amOnPage("/locations/list?menu=stock&item=locations");
        $I->click('#editButton' . $this->id);
        $name = substr(str_shuffle($this->permitted_chars), 0, 2);
        $location = ['name' => $name];
        $I->submitForm('#formUbicacion', $location);
        $I->see('Updated');
    }

    public function delFromLocationListTest(FunctionalTester $I) {
        $I->amOnPage("/locations/list?menu=stock&item=locations");
        $I->click('#delButton' . $this->id);
        $I->dontSeeInDatabase('locations', array('id' => intval($this->id), 'deleted_at' => null));        
        
    }

    public function delFromLocationFormTest(FunctionalTester $I) {
        $I->amOnPage("/locations/list?menu=stock&item=locations");
        $lastRegister = $I->grabNumRecords('locations', array('deleted_at' => null));
        if($lastRegister === 0){
            $this->addLocationTest($I);            
            $lastRegister = $I->grabNumRecords('locations', array('deleted_at' => null));
        }
        $registers = $I->grabColumnFromDatabase('locations', 'id', array('deleted_at' => null));
        $I->click('#editButton' . $registers[$lastRegister - 1]);
        $I->click('Eliminar');
        $I->dontSeeInDatabase('locations', array('id' => intval($registers[$lastRegister - 1]), 'deleted_at' => null));
    }
    
    public function _after(FunctionalTester $I){
        $this->addLocationTest($I);
    }

}
