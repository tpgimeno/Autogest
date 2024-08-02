<?php

namespace Tests\acceptance;

use AcceptanceTester;

class ExpertOpinionComponentsCest
{
    public $permitted_chars;
    protected $id, $opinion_id;  

    public function _before(AcceptanceTester $I) {
        LoginTestCest::LoginPassTest($I);
    }
    // tests
    public function expertOpinionComponentWithoutSave(AcceptanceTester $I) {
        $I->click('Taller');       
        $I->click('#expertOpinions > a');
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
    
    public function expertOpinionComponentSave(AcceptanceTester $I) {
        $I->click('Taller');
        $I->click('#expertOpinions > a');
        $I->click('Nuevo');
        $I->fillField('#expertId', '84684654353G');
        $I->fillField('#date', '16/07/2024');
        $I->wait(2);
        $I->click('#submit');
        $I->wait(2);
        $expertOpinions = $I->grabColumnFromDatabase('expertopinions', 'id', ['deleted_at' => null]);
        $I->click('#components-tab');
        $I->waitForElementClickable('#addAsset', 2);
        $I->click('#addAsset');
        $components = $I->grabColumnFromDatabase('components', 'id', ['deleted_at' => null]);
        $I->click('#editButton' . $components[count($components) - 1]);
        $I->see('Componente');
        $I->fillField('Cantidad', 1);
        $I->click('Guardar');
        $I->wait(2);
        $I->seeInDatabase('expertopinionscomponents', ['expertOpinion_id' => $expertOpinions[count($expertOpinions) - 1]]);
            
    }
    
    public function expertOpinionComponentEdit(AcceptanceTester $I) {
        $I->click('Taller');
        $I->click('#expertOpinions > a');
        $I->click('#dataTable_next');
        $I->click('#dataTable_next');
        $I->click('#dataTable_next');           
        $expertOpinions = $I->grabColumnFromDatabase('expertopinions', 'id', ['deleted_at' => null]);  
        $I->wait(1);
        $I->click('#editButton' . $expertOpinions[count($expertOpinions) - 1]);
        $I->click('#components-tab');
        $expertOpinion_components = $I->grabColumnFromDatabase('expertopinionscomponents', 'id', ['expertOpinion_id' => $expertOpinions[count($expertOpinions) - 1]]);         
        $I->click('#dataTableVehicleComponents #addButton' . $expertOpinion_components[count($expertOpinion_components) - 1]);        
        $I->see('Componente');
        $I->fillField('Cantidad', 2);
        $I->click('Guardar');
        $I->wait(3);
        $I->seeInDatabase('expertopinionscomponents', ['id' => $expertOpinion_components[count($expertOpinion_components) - 1], 'cantity' => 2]);
    }
    
     public function expertOpinionComponentDelete(AcceptanceTester $I) {
        $expertOpinions = $I->grabColumnFromDatabase('expertopinions', 'id', ['deleted_at' => null]);        
        $I->click('Taller');
        $I->click('#expertOpinions > a');
        $I->click('#dataTable_next');
        $I->click('#dataTable_next');
        $I->click('#dataTable_next');                
        $I->click('#editButton' . $expertOpinions[count($expertOpinions) - 1]);
        $I->click('#components-tab');  
        $expertOpinion_components = $I->grabColumnFromDatabase('expertopinionscomponents', 'id', ['expertOpinion_id' => $expertOpinions[count($expertOpinions) - 1]]);        
        $I->click('#dataTableVehicleComponents #delButton' . $expertOpinion_components[count($expertOpinion_components) - 1]); 
        $I->wait(3);
        $I->dontSeeInDatabase('expertopinionscomponents', ['id' => $expertOpinion_components[count($expertOpinion_components) - 1]]);
    }
}
