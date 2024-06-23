<?php

namespace Tests\acceptance;

use AcceptanceTester;

class OrderWorksCest
{
    public $permitted_chars;
    protected $id, $garageOrder_id;  

    public function _before(AcceptanceTester $I) {
        LoginTestCest::LoginPassTest($I);
    }

    // tests
    public function orderWorkWithoutSave(AcceptanceTester $I) {
        $I->click('Taller');
        $I->click('Ordenes Reparacion');
        $I->click('Nuevo');
        $I->click('#works-tab');
        $I->wait(1);
        $I->click('#works-pane #addAsset');
        $works = $I->grabColumnFromDatabase('works', 'id', ['deleted_at' => null]);
        $I->click('#dataTableWorks #editButton' . $works[count($works) - 1]);
        $I->see('Trabajos');
        $I->wait(1);        
        $I->fillField('#garageOrder_work_form #cantity', 1);
        $I->wait(1);
        $I->click('#garageOrder_work_form #total');
        $I->wait(1);
        $I->click('//*[@id="work_form_modal"]/div/div/div[3]/button[1]');
        $I->see('Debe guardar primero!');
    }
    
    public function orderWorkSave(AcceptanceTester $I) {
        $I->click('Taller');
        $I->click('Ordenes Reparacion');
        $I->click('Nuevo'); 
        $I->wait(2);
        $I->click('#submit');
        $orders = $I->grabColumnFromDatabase('garage_orders', 'id', ['deleted_at' => null]);
        $I->click('#works-tab');
        $I->waitForElementClickable('#works-pane #addAsset', 2);
        $I->click('#works-pane #addAsset');
        $works = $I->grabColumnFromDatabase('works', 'id', ['deleted_at' => null]);
        $I->click('#dataTableWorks #editButton' . $works[count($works) - 1]);
        $I->see('Trabajo');
        $I->fillField('#garageOrder_work_form #cantity', 1);
        $I->click('//*[@id="work_form_modal"]/div/div/div[3]/button[1]');
        $I->wait(2);
        $I->seeInDatabase('orderworks', ['garageOrder_id' => $orders[count($orders) - 1], 'work_id' => $works[count($works) - 1]]);
            
    }
    
    public function orderWorkEdit(AcceptanceTester $I) {
        $I->click('Taller');
        $I->click('Ordenes Reparacion');
        $I->click('#dataTable_next');
        $I->click('#dataTable_next');
        $I->click('#dataTable_next'); 
        $I->click('#dataTable_next');
        $I->click('#dataTable_next');  
        $orders = $I->grabColumnFromDatabase('garage_orders', 'id', ['deleted_at' => null]);  
        $I->wait(1);
        $I->click('#editButton' . $orders[count($orders) - 1]);
        $I->click('#works-tab');
        $order_works = $I->grabColumnFromDatabase('orderworks', 'id', ['deleted_at' => null, 'garageOrder_id' => $orders[count($orders) - 1]]);         
        $I->click('#dataTableVehicleWorks #addButton' . $order_works[count($order_works) - 1]);        
        $I->see('Recambio');
        $I->fillField('#garageOrder_work_form #cantity', 2);
        $I->click('//*[@id="work_form_modal"]/div/div/div[3]/button[1]');
        $I->wait(3);
        $I->seeInDatabase('orderworks', ['id' => $order_works[count($order_works) - 1], 'cantity' => 2]);
    }
    
     public function orderWorkDelete(AcceptanceTester $I) {
        $orders = $I->grabColumnFromDatabase('garage_orders', 'id', ['deleted_at' => null]);        
        $I->click('Taller');
        $I->click('Ordenes Reparacion');
        $I->click('#dataTable_next');
        $I->click('#dataTable_next');
        $I->click('#dataTable_next');
        $I->click('#dataTable_next');
        $I->click('#dataTable_next');  
        $I->click('#editButton' . $orders[count($orders) - 1]);
        $I->click('#works-tab');  
        $order_works = $I->grabColumnFromDatabase('orderworks', 'id', ['deleted_at' => null, 'garageOrder_id' => $orders[count($orders) - 1]]);        
        $I->click('#dataTableVehicleWorks #delButton' . $order_works[count($order_works) - 1]); 
        $I->wait(3);
        $I->dontSeeInDatabase('orderworks', ['id' => $order_works[count($order_works) - 1]]);
    }
}
