<?php

namespace Tests\acceptance;

use AcceptanceTester;


class OrderComponentsCest
{
    public $permitted_chars;
    protected $id, $worksheet_id;  

    public function _before(AcceptanceTester $I) {
        LoginTestCest::LoginPassTest($I);
    }

    // tests
    public function orderComponentWithoutSave(AcceptanceTester $I) {
        $I->click('Taller');
        $I->click('Ordenes Reparacion');
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
    
    public function orderComponentSave(AcceptanceTester $I) {
        $I->click('Taller');
        $I->click('Ordenes Reparacion');
        $I->click('Nuevo'); 
        $I->wait(2);
        $I->click('#submit');
        $orders = $I->grabColumnFromDatabase('garage_orders', 'id', ['deleted_at' => null]);
        $I->click('#components-tab');
        $I->waitForElementClickable('#addAsset', 2);
        $I->click('#addAsset');
        $components = $I->grabColumnFromDatabase('components', 'id', ['deleted_at' => null]);
        $I->click('#editButton' . $components[count($components) - 1]);
        $I->see('Componente');
        $I->fillField('Cantidad', 1);
        $I->click('Guardar');
        $I->wait(2);
        $I->seeInDatabase('ordercomponents', ['garageOrder_id' => $orders[count($orders) - 1]]);
            
    }
    
    public function orderComponentEdit(AcceptanceTester $I) {
        $I->click('Taller');
        $I->click('Ordenes Reparacion');
        $I->click('#dataTable_next');
        $I->click('#dataTable_next');
        $I->click('#dataTable_next');           
        $orders = $I->grabColumnFromDatabase('garage_orders', 'id', ['deleted_at' => null]);  
        $I->wait(1);
        $I->click('#editButton' . $orders[count($orders) - 1]);
        $I->click('#components-tab');
        $order_components = $I->grabColumnFromDatabase('ordercomponents', 'id', ['deleted_at' => null, 'garageOrder_id' => $orders[count($orders) - 1]]);         
        $I->click('#dataTableVehicleComponents #addButton' . $order_components[count($order_components) - 1]);        
        $I->see('Componente');
        $I->fillField('Cantidad', 2);
        $I->click('Guardar');
        $I->wait(3);
        $I->seeInDatabase('ordercomponents', ['id' => $order_components[count($order_components) - 1], 'cantity' => 2]);
    }
    
     public function orderComponentDelete(AcceptanceTester $I) {
        $orders = $I->grabColumnFromDatabase('garage_orders', 'id', ['deleted_at' => null]);        
        $I->click('Taller');
        $I->click('Ordenes Reparacion');
        $I->click('#dataTable_next');
        $I->click('#dataTable_next');
        $I->click('#dataTable_next');                
        $I->click('#editButton' . $orders[count($orders) - 1]);
        $I->click('#components-tab');  
        $order_components = $I->grabColumnFromDatabase('ordercomponents', 'id', ['deleted_at' => null, 'garageOrder_id' => $orders[count($orders) - 1]]);        
        $I->click('#dataTableVehicleComponents #delButton' . $order_components[count($order_components) - 1]); 
        $I->wait(3);
        $I->dontSeeInDatabase('ordercomponents', ['id' => $order_components[count($order_components) - 1]]);
    }
}
