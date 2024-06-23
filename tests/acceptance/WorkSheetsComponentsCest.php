<?php

namespace Tests\acceptance;

use AcceptanceTester;

class WorkSheetsComponentsCest
{
    public $permitted_chars;
    protected $id, $worksheet_id;  

    public function _before(AcceptanceTester $I) {
        LoginTestCest::LoginPassTest($I);
    }
    // tests
    public function workSheetComponentWithoutSave(AcceptanceTester $I) {
        $I->click('Taller');
        $I->click('Hojas de taller');
        $I->click('Nuevo');
        $I->click('#components-tab');
        $I->waitForElementClickable('#addAsset', 3);
        $I->click('#addAsset');
        $components = $I->grabColumnFromDatabase('components', 'id', ['deleted_at' => null]);
        $I->click('#editButton' . $components[count($components) - 1]);
        $I->see('Componente');
        $I->fillField('Cantidad', 1);
        $I->click('Guardar');
        $I->see('Debe guardar primero!');
    }
    
    public function workSheetComponentSave(AcceptanceTester $I) {
        $I->click('Taller');
        $I->click('Hojas de taller');
        $I->click('Nuevo'); 
        $I->wait(2);
        $I->click('#submit');
        $I->wait(2);
        $workSheets = $I->grabColumnFromDatabase('worksheets', 'id', ['deleted_at' => null]);
        $I->click('#components-tab');
        $I->waitForElementClickable('#addAsset', 2);
        $I->click('#addAsset');
        $components = $I->grabColumnFromDatabase('components', 'id', ['deleted_at' => null]);
        $I->click('#editButton' . $components[count($components) - 1]);
        $I->see('Componente');
        $I->fillField('Cantidad', 1);
        $I->click('Guardar');
        $I->wait(2);
        $I->seeInDatabase('worksheetscomponents', ['worksheet_id' => $workSheets[count($workSheets) - 1]]);
            
    }
    
    public function workSheetComponentEdit(AcceptanceTester $I) {
        $I->click('Taller');
        $I->click('Hojas de taller');
        $I->click('#dataTable_next');
        $I->click('#dataTable_next');
        $I->click('#dataTable_next');           
        $workSheets = $I->grabColumnFromDatabase('worksheets', 'id', ['deleted_at' => null]);  
        $I->wait(1);
        $I->click('#editButton' . $workSheets[count($workSheets) - 1]);
        $I->click('#components-tab');
        $workSheet_components = $I->grabColumnFromDatabase('worksheetscomponents', 'id', ['deleted_at' => null, 'worksheet_id' => $workSheets[count($workSheets) - 1]]);         
        $I->click('#dataTableVehicleComponents #addButton' . $workSheet_components[count($workSheet_components) - 1]);        
        $I->see('Componente');
        $I->fillField('Cantidad', 2);
        $I->click('Guardar');
        $I->wait(3);
        $I->seeInDatabase('worksheetscomponents', ['id' => $workSheet_components[count($workSheet_components) - 1], 'cantity' => 2]);
    }
    
     public function workSheetComponentDelete(AcceptanceTester $I) {
        $workSheets = $I->grabColumnFromDatabase('worksheets', 'id', ['deleted_at' => null]);        
        $I->click('Taller');
        $I->click('Hojas de taller');
        $I->click('#dataTable_next');
        $I->click('#dataTable_next');
        $I->click('#dataTable_next');                
        $I->click('#editButton' . $workSheets[count($workSheets) - 1]);
        $I->click('#components-tab');  
        $workSheet_components = $I->grabColumnFromDatabase('worksheetscomponents', 'id', ['deleted_at' => null, 'worksheet_id' => $workSheets[count($workSheets) - 1]]);        
        $I->click('#dataTableVehicleComponents #delButton' . $workSheet_components[count($workSheet_components) - 1]); 
        $I->wait(3);
        $I->dontSeeInDatabase('worksheetscomponents', ['id' => $workSheet_components[count($workSheet_components) - 1]]);
    }
}
