<?php


namespace Tests\acceptance;

use AcceptanceTester;

class OrderSuppliesCest
{
    public $permitted_chars;
    protected $id, $garageOrder_id;  

    public function _before(AcceptanceTester $I) {
        LoginTestCest::LoginPassTest($I);
    }

    // tests
    public function orderSupplyWithoutSave(AcceptanceTester $I) {
        $I->click('Taller');
        $I->click('Ordenes Reparacion');
        $I->click('Nuevo');
        $I->click('#supplies-tab');
        $I->waitForElementClickable('#supplies-pane #addAsset', 3);
        $I->click('#supplies-pane #addAsset');
        $supplies = $I->grabColumnFromDatabase('supplies', 'id', ['deleted_at' => null]);
        $I->click('#editButton' . $supplies[count($supplies) - 1]);
        $I->see('Recambio');
        $I->fillField('#garageOrder_supply_form #cantity', 1);
        $I->click('//*[@id="supply_form_modal"]/div/div/div[3]/button[1]');
        $I->see('Debe guardar primero!');
    }
    
    public function orderSupplySave(AcceptanceTester $I) {
        $I->click('Taller');
        $I->click('Ordenes Reparacion');
        $I->click('Nuevo'); 
        $I->wait(2);
        $I->click('#submit');
        $orders = $I->grabColumnFromDatabase('garage_orders', 'id', ['deleted_at' => null]);
        $I->click('#supplies-tab');
        $I->waitForElementClickable('#supplies-pane #addAsset', 2);
        $I->click('#supplies-pane #addAsset');
        $supplies = $I->grabColumnFromDatabase('supplies', 'id', ['deleted_at' => null]);
        $I->click('#editButton' . $supplies[count($supplies) - 1]);
        $I->see('Recambio');
        $I->fillField('#garageOrder_supply_form #cantity', 1);
        $I->click('//*[@id="supply_form_modal"]/div/div/div[3]/button[1]');
        $I->wait(2);
        $I->seeInDatabase('ordersupplies', ['garageOrder_id' => $orders[count($orders) - 1], 'supply_id' => $supplies[count($supplies) - 1]]);
            
    }
    
    public function orderSupplyEdit(AcceptanceTester $I) {
        $I->click('Taller');
        $I->click('Ordenes Reparacion');
        $I->click('#dataTable_next');
        $I->click('#dataTable_next');
        $I->click('#dataTable_next');           
        $orders = $I->grabColumnFromDatabase('garage_orders', 'id', ['deleted_at' => null]);  
        $I->wait(1);
        $I->click('#editButton' . $orders[count($orders) - 1]);
        $I->click('#supplies-tab');
        $offer_supplies = $I->grabColumnFromDatabase('ordersupplies', 'id', ['deleted_at' => null, 'garageOrder_id' => $orders[count($orders) - 1]]);         
        $I->click('#dataTableVehicleSupplies #addButton' . $offer_supplies[count($offer_supplies) - 1]);        
        $I->see('Recambio');
        $I->fillField('#garageOrder_supply_form #cantity', 2);
        $I->click('//*[@id="supply_form_modal"]/div/div/div[3]/button[1]');
        $I->wait(3);
        $I->seeInDatabase('ordersupplies', ['id' => $offer_supplies[count($offer_supplies) - 1], 'cantity' => 2]);
    }
    
     public function orderSupplyDelete(AcceptanceTester $I) {
        $orders = $I->grabColumnFromDatabase('garage_orders', 'id', ['deleted_at' => null]);        
        $I->click('Taller');
        $I->click('Ordenes Reparacion');
        $I->click('#dataTable_next');
        $I->click('#dataTable_next');
        $I->click('#dataTable_next');                
        $I->click('#editButton' . $orders[count($orders) - 1]);
        $I->click('#supplies-tab');  
        $offer_supplies = $I->grabColumnFromDatabase('ordersupplies', 'id', ['deleted_at' => null, 'garageOrder_id' => $orders[count($orders) - 1]]);        
        $I->click('#dataTableVehicleSupplies #delButton' . $offer_supplies[count($offer_supplies) - 1]); 
        $I->wait(3);
        $I->dontSeeInDatabase('ordersupplies', ['id' => $offer_supplies[count($offer_supplies) - 1]]);
    }
}
