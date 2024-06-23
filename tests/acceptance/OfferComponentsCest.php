<?php

namespace Tests\acceptance;

use AcceptanceTester;

class OfferComponentsCest
{
    public $permitted_chars;
    protected $id, $selloffer_id;  

    public function _before(AcceptanceTester $I) {
        LoginTestCest::LoginPassTest($I);
    }

    // tests
    public function offerComponentWithoutSave(AcceptanceTester $I) {
        $I->click('Ventas');
        $I->click('Ofertas');
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
    
    public function offerComponentSave(AcceptanceTester $I) {
        $I->click('Ventas');
        $I->click('Ofertas');
        $I->click('Nuevo'); 
        $I->wait(2);
        $I->click('#submit');
        $offers = $I->grabColumnFromDatabase('selloffers', 'id', ['deleted_at' => null]);
        $I->click('#components-tab');
        $I->waitForElementClickable('#addAsset', 2);
        $I->click('#addAsset');
        $components = $I->grabColumnFromDatabase('components', 'id', ['deleted_at' => null]);
        $I->click('#editButton' . $components[count($components) - 1]);
        $I->see('Componente');
        $I->fillField('Cantidad', 1);
        $I->click('Guardar');
        $I->wait(2);
        $I->seeInDatabase('sellofferscomponents', ['selloffer_id' => $offers[count($offers) - 1]]);
            
    }
    
    public function offerComponentEdit(AcceptanceTester $I) {
        $I->click('Ventas');
        $I->click('Ofertas');
        $I->click('#dataTable_next');
        $I->click('#dataTable_next');
        $I->click('#dataTable_next');           
        $offers = $I->grabColumnFromDatabase('selloffers', 'id', ['deleted_at' => null]);  
        $I->wait(1);
        $I->click('#editButton' . $offers[count($offers) - 1]);
        $I->click('#components-tab');
        $offer_components = $I->grabColumnFromDatabase('sellofferscomponents', 'id', ['deleted_at' => null, 'selloffer_id' => $offers[count($offers) - 1]]);         
        $I->click('#dataTableVehicleComponents #addButton' . $offer_components[count($offer_components) - 1]);        
        $I->see('Componente');
        $I->fillField('Cantidad', 2);
        $I->click('Guardar');
        $I->wait(3);
        $I->seeInDatabase('sellofferscomponents', ['id' => $offer_components[count($offer_components) - 1], 'cantity' => 2]);
    }
    
     public function offerComponentDelete(AcceptanceTester $I) {
        $offers = $I->grabColumnFromDatabase('selloffers', 'id', ['deleted_at' => null]);        
        $I->click('Ventas');
        $I->click('Ofertas');
        $I->click('#dataTable_next');
        $I->click('#dataTable_next');
        $I->click('#dataTable_next');                
        $I->click('#editButton' . $offers[count($offers) - 1]);
        $I->click('#components-tab');  
        $offer_components = $I->grabColumnFromDatabase('sellofferscomponents', 'id', ['deleted_at' => null, 'selloffer_id' => $offers[count($offers) - 1]]);        
        $I->click('#dataTableVehicleComponents #delButton' . $offer_components[count($offer_components) - 1]); 
        $I->wait(3);
        $I->dontSeeInDatabase('sellofferscomponents', ['id' => $offer_components[count($offer_components) - 1]]);
    }
    
}
