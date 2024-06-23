<?php

namespace Tests\acceptance;

use AcceptanceTester;

class WorkSheetsWorksCest
{
    public $permitted_chars;
    protected $id, $worksheet_id;  

    public function _before(AcceptanceTester $I) {
        LoginTestCest::LoginPassTest($I);
    }

    // tests
    public function workSheetWorkWithoutSave(AcceptanceTester $I) {
        $I->click('Taller');
        $I->click('//*[@id="workSheets"]/a');
        $I->click('Nuevo');
        $I->click('#works-tab');
        $I->wait(1);
        $I->click('#works-pane #addAsset');
        $works = $I->grabColumnFromDatabase('works', 'id', ['deleted_at' => null]);
        $I->click('#dataTableWorks #editButton' . $works[count($works) - 1]);
        $I->see('Trabajos');
        $I->wait(1);        
        $I->fillField('#workSheets_work_form #cantity', 1);
        $I->wait(1);
        $I->click('#workSheets_work_form #total');
        $I->wait(1);
        $I->click('//*[@id="work_form_modal"]/div/div/div[3]/button[1]');
        $I->see('Debe guardar primero!');
    }
    
    public function workSheetWorkSave(AcceptanceTester $I) {
        $I->click('Taller');
        $I->click('//*[@id="workSheets"]/a');
        $I->click('Nuevo'); 
        $I->wait(2);
        $I->click('#submit');
        $worksheets = $I->grabColumnFromDatabase('worksheets', 'id', ['deleted_at' => null]);
        $I->click('#works-tab');
        $I->waitForElementClickable('#works-pane #addAsset', 2);
        $I->click('#works-pane #addAsset');
        $works = $I->grabColumnFromDatabase('works', 'id', ['deleted_at' => null]);
        $I->click('#dataTableWorks #editButton' . $works[count($works) - 1]);
        $I->see('Trabajo');
        $I->fillField('#workSheets_work_form #cantity', 1);
        $I->click('//*[@id="work_form_modal"]/div/div/div[3]/button[1]');
        $I->wait(2);
        $I->seeInDatabase('worksheetsworks', ['worksheet_id' => $worksheets[count($worksheets) - 1], 'work_id' => $works[count($works) - 1]]);
            
    }
    
    public function workSheetWorkEdit(AcceptanceTester $I) {
        $I->click('Taller');
        $I->click('//*[@id="workSheets"]/a');
        $I->click('#dataTable_next');
        $I->click('#dataTable_next');
        $I->click('#dataTable_next'); 
        $I->click('#dataTable_next');
        $I->click('#dataTable_next');  
        $worksheets = $I->grabColumnFromDatabase('worksheets', 'id', ['deleted_at' => null]);  
        $I->wait(1);
        $I->click('#editButton' . $worksheets[count($worksheets) - 1]);
        $I->click('#works-tab');
        $worksheets_works = $I->grabColumnFromDatabase('worksheetsworks', 'id', ['deleted_at' => null, 'worksheet_id' => $worksheets[count($worksheets) - 1]]);         
        $I->click('#dataTableVehicleWorks #addButton' . $worksheets_works[count($worksheets_works) - 1]);        
        $I->see('Recambio');
        $I->fillField('#workSheets_work_form #cantity', 2);
        $I->click('//*[@id="work_form_modal"]/div/div/div[3]/button[1]');
        $I->wait(3);
        $I->seeInDatabase('worksheetsworks', ['id' => $worksheets_works[count($worksheets_works) - 1], 'cantity' => 2]);
    }
    
     public function workSheetWorkDelete(AcceptanceTester $I) {
        $worksheets = $I->grabColumnFromDatabase('worksheets', 'id', ['deleted_at' => null]);        
        $I->click('Taller');
        $I->click('//*[@id="workSheets"]/a');
        $I->click('#dataTable_next');
        $I->click('#dataTable_next');
        $I->click('#dataTable_next');
        $I->click('#dataTable_next');
        $I->click('#dataTable_next');  
        $I->click('#editButton' . $worksheets[count($worksheets) - 1]);
        $I->click('#works-tab');  
        $worksheets_works = $I->grabColumnFromDatabase('worksheetsworks', 'id', ['deleted_at' => null, 'worksheet_id' => $worksheets[count($worksheets) - 1]]);        
        $I->click('#dataTableVehicleWorks #delButton' . $worksheets_works[count($worksheets_works) - 1]); 
        $I->wait(3);
        $I->dontSeeInDatabase('worksheetsworks', ['id' => $worksheets_works[count($worksheets_works) - 1]]);
    }
}
