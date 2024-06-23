<?php

class GarageOrdersCest
{
    protected $id, $permitted_chars;
     
    public function _before(FunctionalTester $I) {
        $I->amOnPage("/");
        $I->submitForm('#loginForm', ['email' => 'tonyllomouse@gmail.com', 'password' => '12345']);
    }

    // tests
    public function addGarageOrderTest(FunctionalTester $I) {
        $I->amOnPage("/garageOrders/list?menu=garages&item=orders");
        $I->click('#newButton');
        $this->permitted_chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';       
        $customer = $I->grabColumnFromDatabase('customers', 'id', ['deleted_at' => null]);
        $vehicle = $I->grabColumnFromDatabase('vehicles', 'id', ['deleted_at' => null]);
        $lastOrders = $I->grabColumnFromDatabase('garage_orders', 'orderNumber', []); 
        $numberOrder = intval(substr($lastOrders[count($lastOrders) - 1], 2, strlen($lastOrders[count($lastOrders) - 1])))+1;
        $orderNumber = 'OV' . strval($numberOrder);
        $garageOrder = ['orderNumber' => $orderNumber, 
            'customer_id' => $customer[count($customer) - 1],
            'inDate' => '12/12/2023', 
            'outDate' => '13/12/2023', 
            'vehicle_id' => $vehicle[count($vehicle) - 1],            
            'baseOrder' => 0, 'discountOrder' => 0, 'tvaOrder' => 0, 'totalOrder' => 0];
        $I->submitForm('#formOrdendeTrabajo', $garageOrder);        
        $this->id = $I->grabFromDatabase('garage_orders', 'id', ['orderNumber' => $orderNumber, 'deleted_at' => null]);        
        $I->seeInDatabase('garage_orders', ['orderNumber' => $orderNumber]);
    }

    public function editGarageOrderTest(FunctionalTester $I) {
        $I->amOnPage("/garageOrders/list?menu=garages&item=orders");
        $I->click('#editButton' . $this->id);        
        $garageOrder = ['outDate' => '15/12/2021'];
        $I->submitForm('#formOrdendeTrabajo', $garageOrder);    
        $I->see('Updated');
    }

    public function delFromGarageOrdersListTest(FunctionalTester $I) {
        $I->amOnPage("/garageOrders/list?menu=garages&item=orders");
        $I->click('#delButton' . $this->id);
        $I->dontSeeInDatabase('garage_orders', array('id' => intval($this->id), 'deleted_at' => null));
    }

    public function delFromGarageOrderFormTest(FunctionalTester $I) {
        $this->addGarageOrderTest($I);
        $this->_before($I);
        $I->amOnPage("/garageOrders/list?menu=garages&item=orders");        
        $I->click('#editButton' . $this->id);
        $I->click('Eliminar');
        $I->dontSeeInDatabase('garage_orders', array('id' => intval($this->id), 'deleted_at' => null));
    }
}
