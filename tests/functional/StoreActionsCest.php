<?php

class StoreActionsCest {

    public $permitted_chars;
    protected $id;
    protected $name;

    public function _before(FunctionalTester $I) {
        $I->amOnPage("/");
        $I->submitForm('#loginForm', ['email' => 'tonyllomouse@gmail.com', 'password' => '12345']);
    }

    // tests
    public function addStoreTest(FunctionalTester $I) {
        $I->amOnPage("/stores/list?menu=stock&item=stores");
        $I->click('#newButton');
        $this->permitted_chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $this->name = substr(str_shuffle($this->permitted_chars), 0, 16);
        $postalCode = random_int(10000, 99999);
        $almacen = ['name' => $this->name, 'address' => 'C/ Lorem Ipsum, 123', 'postal_code' => $postalCode, 'city' => 'Ipsum', 'state' => 'Lorem', 'country' => 'IpsumLorem', 'phone' => '962541144', 'email' => 'loremipsum@loremipsum.com'];
        $I->submitForm('#formAlmacen', $almacen);
        $this->id = $I->grabFromDatabase('stores', 'id', array('name' => $this->name));
        $I->see('Saved');
    }

    public function editStoreTest(FunctionalTester $I) {
        $I->amOnPage("/stores/list?menu=stock&item=stores");
        $I->click('#editButton' . $this->id);
        $almacen = ['id' => $this->id, 'name' => 'LoremIpsumEdited', 'address' => 'C/ Lorem Ipsum, 123', 'postal_code' => random_int(10000, 99999), 'city' => 'Ipsum', 'state' => 'Lorem', 'country' => 'IpsumLorem', 'phone' => '962541144', 'email' => 'loremipsum@loremipsum.com'];
        $I->submitForm('#formAlmacen', $almacen);
        $I->see('Updated');
    }

    public function delFromStoreListTest(FunctionalTester $I) {
        $I->amOnPage("/stores/list?menu=stock&item=stores");
        $I->click('#delButton' . $this->id);
        $I->dontSeeInDatabase('stores', array('id' => intval($this->id), 'deleted_at' => null));
    }

    public function delFromStoreFormTest(FunctionalTester $I) {
        $I->amOnPage("/stores/list?menu=stock&item=stores");
        $lastRegister = $I->grabNumRecords('stores', array('deleted_at' => null));
        if ($lastRegister === 0) {
            $this->addStoreTest($I);
            $lastRegister = $I->grabNumRecords('stores', array('deleted_at' => null));
        }
        $registers = $I->grabColumnFromDatabase('stores', 'id', array('deleted_at' => null));
        $I->click('#editButton' . $registers[$lastRegister - 1]);
        $I->click('Eliminar');
        $I->dontSeeInDatabase('stores', array('id' => intval($registers[$lastRegister - 1]), 'deleted_at' => null));
    }
    
    public function _after(FunctionalTester $I){
        $this->addStoreTest($I);
    }

}
