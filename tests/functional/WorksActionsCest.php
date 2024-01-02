<?php

class WorksActionsCest
{
    protected $name,$ref, $id, $permitted_chars;
    public function _before(FunctionalTester $I) {
        $I->amOnPage("/");
        $I->submitForm('#loginForm', ['email' => 'tonyllomouse@gmail.com', 'password' => '12345']);
    }

    // tests
    public function addWorkTest(FunctionalTester $I) {
        $I->amOnPage("/vehicles/works/list?menu=stock&item=works");
        $I->click('#newButton');
        $this->permitted_chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $this->name = substr(str_shuffle($this->permitted_chars), 0, 12);
        $this->ref = substr(str_shuffle($this->permitted_chars), 0, 20);        
        $work = ['name' => $this->name, 'ref' => $this->ref];
        $I->submitForm('#formTrabajo', $work);        
        $this->id = $I->grabFromDatabase('works', 'id', ['name' => $this->name]);        
        $I->seeInDatabase('works', ['name' => $this->name]);
    }

    public function editWorkTest(FunctionalTester $I) {
        $I->amOnPage("/vehicles/works/list?menu=stock&item=works");
        $I->click('#editButton' . $this->id);        
        $this->ref = substr(str_shuffle($this->permitted_chars), 0, 20);        
        $work = ['name' => $this->name, 'ref' => $this->ref];
        $I->submitForm('#formTrabajo', $work);  
        $I->see('Updated');
    }

    public function delFromWorksListTest(FunctionalTester $I) {
        $I->amOnPage("/vehicles/works/list?menu=stock&item=works");
        $I->click('#delButton' . $this->id);
        $I->dontSeeInDatabase('works', array('id' => intval($this->id), 'deleted_at' => null));
    }

    public function delFromWorkFormTest(FunctionalTester $I) {
        $this->addWorkTest($I);
        $this->_before($I);
        $I->amOnPage("/vehicles/works/list?menu=stock&item=works");        
        $I->click('#editButton' . $this->id);
        $I->click('Eliminar');
        $I->dontSeeInDatabase('works', array('id' => intval($this->id), 'deleted_at' => null));
    }
}
