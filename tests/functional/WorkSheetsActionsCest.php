<?php


class WorkSheetsActionsCest 
{
        
    protected $id, $permitted_chars;
     
    public function _before(FunctionalTester $I) {
        $I->amOnPage("/");
        $I->submitForm('#loginForm', ['email' => 'tonyllomouse@gmail.com', 'password' => '12345']);
    }

    // tests
    public function addWorkSheetTest(FunctionalTester $I) {
        $I->amOnPage("/workSheets/list?menu=garagesk&item=workSheets");
        $I->click('#newButton');
        $this->permitted_chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';       
        $customer = $I->grabColumnFromDatabase('customers', 'id', ['deleted_at' => null]);
        $vehicle = $I->grabColumnFromDatabase('vehicles', 'id', ['deleted_at' => null]);
        $garageOrder = $I->grabColumnFromDatabase('garage_orders', 'id', ['deleted_at' => null]);
        $lastWorkSheet = $I->grabColumnFromDatabase('worksheets', 'workSheetNumber', []); 
        $workId = $I->grabColumnFromDatabase('works', 'id', ['deleted_at' => null]);
        $numberWorkSheet = intval(substr($lastWorkSheet[count($lastWorkSheet) - 1], 2, strlen($lastWorkSheet[count($lastWorkSheet) - 1])))+1;
        $workSheetNumber = 'OT' . strval($numberWorkSheet);
        $workSheet = ['workSheetNumber' => $workSheetNumber,'order_id' => $garageOrder[count($garageOrder) - 1],'customer_id' => $customer[count($customer) - 1],'vehicle_id' => $vehicle[count($vehicle) -1],'inDate' => '16/06/2024 12:40:51','outDate' => '17/06/2024 12:40:51','text' => 'Texto','observations' => 'Observaciones', 'work_id' => $workId];
        $I->submitForm('#formHojadeTrabajo', $workSheet);        
        $this->id = $I->grabFromDatabase('worksheets', 'id', ['workSheetNumber' => $workSheetNumber, 'deleted_at' => null]);        
        $I->seeInDatabase('worksheets', ['workSheetNumber' => $workSheetNumber]);
    }

    public function editWorkSheetTest(FunctionalTester $I) {
        $I->amOnPage("/workSheets/list?menu=garagesk&item=workSheets");
        $I->click('#editButton' . $this->id);        
        $workSheet = ['outDate' => '16/12/2023'];
        $I->submitForm('#formHojadeTrabajo', $workSheet);    
        $I->see('Updated');
    }

    public function delFromWorkSheetListTest(FunctionalTester $I) {
        $I->amOnPage("/workSheets/list?menu=garagesk&item=workSheets");
        $I->click('#delButton' . $this->id);
        $I->dontSeeInDatabase('worksheets', array('id' => intval($this->id), 'deleted_at' => null));
    }

    public function delFromWorkSheetFormTest(FunctionalTester $I) {
        $this->addWorkSheetTest($I);
        $this->_before($I);
        $I->amOnPage("/workSheets/list?menu=garagesk&item=workSheets");        
        $I->click('#editButton' . $this->id);
        $I->click('Eliminar');
        $I->dontSeeInDatabase('worksheets', array('id' => intval($this->id), 'deleted_at' => null));
    }
}