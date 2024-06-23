<?php

namespace Tests\acceptance;

use AcceptanceTester;

class WorkSheetsSuppliesCest
{
    public $permitted_chars;
    protected $id, $workSheet_id;  

    public function _before(AcceptanceTester $I) {
        LoginTestCest::LoginPassTest($I);
    }

    // tests
    public function workSheetSupplyWithoutSave(AcceptanceTester $I) {
        $I->click('Taller');
        $I->click('Hojas de taller');
        $I->click('Nuevo');
        $I->click('#supplies-tab');
        $I->waitForElementClickable('#supplies-pane #addAsset', 3);
        $I->click('#supplies-pane #addAsset');
        $supplies = $I->grabColumnFromDatabase('supplies', 'id', ['deleted_at' => null]);
        $I->click('#editButton' . $supplies[count($supplies) - 1]);
        $I->see('Recambio');
        $I->fillField('#workSheets_supply_form #cantity', 1);
        $I->click('//*[@id="supply_form_modal"]/div/div/div[3]/button[1]');
        $I->see('Debe guardar primero!');
    }
    
    public function workSheetSupplySave(AcceptanceTester $I) {
        $I->click('Taller');
        $I->click('Hojas de taller');
        $I->click('Nuevo'); 
        $I->wait(2);
        $I->click('#submit');
        $worksheets = $I->grabColumnFromDatabase('worksheets', 'id', ['deleted_at' => null]);
        $I->click('#supplies-tab');
        $I->waitForElementClickable('#supplies-pane #addAsset', 2);
        $I->click('#supplies-pane #addAsset');
        $supplies = $I->grabColumnFromDatabase('supplies', 'id', ['deleted_at' => null]);
        $I->click('#editButton' . $supplies[count($supplies) - 1]);
        $I->see('Recambio');
        $I->fillField('#workSheets_supply_form #cantity', 1);
        $I->click('//*[@id="supply_form_modal"]/div/div/div[3]/button[1]');
        $I->wait(2);
        $I->seeInDatabase('worksheetssupplies', ['worksheet_id' => $worksheets[count($worksheets) - 1], 'supply_id' => $supplies[count($supplies) - 1]]);
            
    }
    
    public function workSheetSupplyEdit(AcceptanceTester $I) {
        $I->click('Taller');
        $I->click('Hojas de taller');
        $I->click('#dataTable_next');
        $I->click('#dataTable_next');
        $I->click('#dataTable_next');           
        $worksheets = $I->grabColumnFromDatabase('worksheets', 'id', ['deleted_at' => null]);  
        $I->wait(1);
        $I->click('#editButton' . $worksheets[count($worksheets) - 1]);
        $I->click('#supplies-tab');
        $worksheet_supplies = $I->grabColumnFromDatabase('worksheetssupplies', 'id', ['deleted_at' => null, 'worksheet_id' => $worksheets[count($worksheets) - 1]]);         
        $I->click('#dataTableVehicleSupplies #addButton' . $worksheet_supplies[count($worksheet_supplies) - 1]);        
        $I->see('Recambio');
        $I->fillField('#workSheets_supply_form #cantity', 2);
        $I->click('//*[@id="supply_form_modal"]/div/div/div[3]/button[1]');
        $I->wait(2);
        $I->seeInDatabase('worksheetssupplies', ['id' => $worksheet_supplies[count($worksheet_supplies) - 1], 'cantity' => 2]);
    }
    
     public function workSheetSupplyDelete(AcceptanceTester $I) {
        $worksheets = $I->grabColumnFromDatabase('worksheets', 'id', ['deleted_at' => null]);        
        $I->click('Taller');
        $I->click('Hojas de taller');
        $I->click('#dataTable_next');
        $I->click('#dataTable_next');
        $I->click('#dataTable_next');                
        $I->click('#editButton' . $worksheets[count($worksheets) - 1]);
        $I->click('#supplies-tab');  
        $worksheet_supplies = $I->grabColumnFromDatabase('worksheetssupplies', 'id', ['deleted_at' => null, 'worksheet_id' => $worksheets[count($worksheets) - 1]]);        
        $I->click('#dataTableVehicleSupplies #delButton' . $worksheet_supplies[count($worksheet_supplies) - 1]); 
        $I->wait(3);
        $I->dontSeeInDatabase('worksheetssupplies', ['id' => $worksheet_supplies[count($worksheet_supplies) - 1]]);
    }
}
