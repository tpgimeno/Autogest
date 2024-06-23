<?php

namespace Tests\acceptance;

use AcceptanceTester;

class OfferSuppliesCest
{
    public $permitted_chars;
    protected $id, $selloffer_id;  

    public function _before(AcceptanceTester $I) {
        LoginTestCest::LoginPassTest($I);
    }

    // tests
    public function offerSupplyWithoutSave(AcceptanceTester $I) {
        $I->click('Ventas');
        $I->click('Ofertas');
        $I->click('Nuevo');
        $I->click('#supplies-tab');
        $I->wait(1);
        $I->click('#supplies-pane #addAsset');
        $supplies = $I->grabColumnFromDatabase('supplies', 'id', ['deleted_at' => null]);
        $I->click('#editButton' . $supplies[count($supplies) - 1]);
        $I->see('Recambio');        
        $I->fillField('#sellOffer_supply_form #cantity', 1);
        $I->wait(1);
        $I->click('#sellOffer_supply_form #total');
        $I->wait(1);
        $I->click('//*[@id="supply_form_modal"]/div/div/div[3]/button[1]');
        $I->see('Debe guardar primero!');
    }
    
    public function offerSupplySave(AcceptanceTester $I) {
        $I->click('Ventas');
        $I->click('Ofertas');
        $I->click('Nuevo'); 
        $I->wait(2);
        $I->click('#submit');
        $offers = $I->grabColumnFromDatabase('selloffers', 'id', ['deleted_at' => null]);
        $I->click('#supplies-tab');
        $I->waitForElementClickable('#supplies-pane #addAsset', 2);
        $I->click('#supplies-pane #addAsset');
        $supplies = $I->grabColumnFromDatabase('supplies', 'id', ['deleted_at' => null]);
        $I->click('#editButton' . $supplies[count($supplies) - 1]);
        $I->see('Recambio');
        $I->fillField('#sellOffer_supply_form #cantity', 1);
        $I->click('//*[@id="supply_form_modal"]/div/div/div[3]/button[1]');
        $I->wait(2);
        $I->seeInDatabase('sellofferssupplies', ['selloffer_id' => $offers[count($offers) - 1], 'supply_id' => $supplies[count($supplies) - 1]]);
            
    }
    
    public function offerSupplyEdit(AcceptanceTester $I) {
        $I->click('Ventas');
        $I->click('Ofertas');
        $I->click('#dataTable_next');
        $I->click('#dataTable_next');
        $I->click('#dataTable_next');           
        $offers = $I->grabColumnFromDatabase('selloffers', 'id', ['deleted_at' => null]);  
        $I->wait(1);
        $I->click('#editButton' . $offers[count($offers) - 1]);
        $I->click('#supplies-tab');
        $offer_supplies = $I->grabColumnFromDatabase('sellofferssupplies', 'id', ['deleted_at' => null, 'selloffer_id' => $offers[count($offers) - 1]]);         
        $I->click('#dataTableVehicleSupplies #addButton' . $offer_supplies[count($offer_supplies) - 1]);        
        $I->see('Recambio');
        $I->fillField('#sellOffer_supply_form #cantity', 2);
        $I->click('//*[@id="supply_form_modal"]/div/div/div[3]/button[1]');
        $I->wait(3);
        $I->seeInDatabase('sellofferssupplies', ['id' => $offer_supplies[count($offer_supplies) - 1], 'cantity' => 2]);
    }
    
     public function offerSupplyDelete(AcceptanceTester $I) {
        $offers = $I->grabColumnFromDatabase('selloffers', 'id', ['deleted_at' => null]);        
        $I->click('Ventas');
        $I->click('Ofertas');
        $I->click('#dataTable_next');
        $I->click('#dataTable_next');
        $I->click('#dataTable_next');                
        $I->click('#editButton' . $offers[count($offers) - 1]);
        $I->click('#supplies-tab');  
        $offer_supplies = $I->grabColumnFromDatabase('sellofferssupplies', 'id', ['deleted_at' => null, 'selloffer_id' => $offers[count($offers) - 1]]);        
        $I->click('#dataTableVehicleSupplies #delButton' . $offer_supplies[count($offer_supplies) - 1]); 
        $I->wait(3);
        $I->dontSeeInDatabase('sellofferssupplies', ['id' => $offer_supplies[count($offer_supplies) - 1]]);
    }
}
