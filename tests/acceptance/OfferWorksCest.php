<?php

namespace Tests\acceptance;

use AcceptanceTester;

class OfferWorksCest
{
    public $permitted_chars;
    protected $id, $selloffer_id;  

    public function _before(AcceptanceTester $I) {
        LoginTestCest::LoginPassTest($I);
    }

    // tests
    public function offerWorkWithoutSave(AcceptanceTester $I) {
        $I->click('Ventas');
        $I->click('Ofertas');
        $I->click('Nuevo');
        $I->click('#works-tab');
        $I->wait(1);
        $I->click('#works-pane #addAsset');
        $works = $I->grabColumnFromDatabase('works', 'id', ['deleted_at' => null]);
        $I->click('#dataTableWorks #editButton' . $works[count($works) - 1]);
        $I->see('Trabajos');
        $I->wait(1);        
        $I->fillField('#sellOffer_work_form #cantity', 1);
        $I->wait(1);
        $I->click('#sellOffer_work_form #total');
        $I->wait(1);
        $I->click('//*[@id="work_form_modal"]/div/div/div[3]/button[1]');
        $I->see('Debe guardar primero!');
    }
    
    public function offerSupplySave(AcceptanceTester $I) {
        $I->click('Ventas');
        $I->click('Ofertas');
        $I->click('Nuevo'); 
        $I->wait(2);
        $I->click('#submit');
        $offers = $I->grabColumnFromDatabase('selloffers', 'id', ['deleted_at' => null]);
        $I->click('#works-tab');
        $I->waitForElementClickable('#works-pane #addAsset', 2);
        $I->click('#works-pane #addAsset');
        $works = $I->grabColumnFromDatabase('works', 'id', ['deleted_at' => null]);
        $I->click('#dataTableWorks #editButton' . $works[count($works) - 1]);
        $I->see('Trabajo');
        $I->fillField('#sellOffer_work_form #cantity', 1);
        $I->click('//*[@id="work_form_modal"]/div/div/div[3]/button[1]');
        $I->wait(2);
        $I->seeInDatabase('selloffersworks', ['selloffer_id' => $offers[count($offers) - 1], 'work_id' => $works[count($works) - 1]]);
            
    }
    
    public function offerSupplyEdit(AcceptanceTester $I) {
        $I->click('Ventas');
        $I->click('Ofertas');
        $I->click('#dataTable_next');
        $I->click('#dataTable_next');
        $I->click('#dataTable_next'); 
        $I->click('#dataTable_next');
        $I->click('#dataTable_next');  
        $offers = $I->grabColumnFromDatabase('selloffers', 'id', ['deleted_at' => null]);  
        $I->wait(1);
        $I->click('#editButton' . $offers[count($offers) - 1]);
        $I->click('#works-tab');
        $offer_works = $I->grabColumnFromDatabase('selloffersworks', 'id', ['deleted_at' => null, 'selloffer_id' => $offers[count($offers) - 1]]);         
        $I->click('#dataTableVehicleWorks #addButton' . $offer_works[count($offer_works) - 1]);        
        $I->see('Recambio');
        $I->fillField('#sellOffer_work_form #cantity', 2);
        $I->click('//*[@id="work_form_modal"]/div/div/div[3]/button[1]');
        $I->wait(3);
        $I->seeInDatabase('selloffersworks', ['id' => $offer_works[count($offer_works) - 1], 'cantity' => 2]);
    }
    
     public function offerSupplyDelete(AcceptanceTester $I) {
        $offers = $I->grabColumnFromDatabase('selloffers', 'id', ['deleted_at' => null]);        
        $I->click('Ventas');
        $I->click('Ofertas');
        $I->click('#dataTable_next');
        $I->click('#dataTable_next');
        $I->click('#dataTable_next');
        $I->click('#dataTable_next');
        $I->click('#dataTable_next');  
        $I->click('#editButton' . $offers[count($offers) - 1]);
        $I->click('#works-tab');  
        $offer_works = $I->grabColumnFromDatabase('selloffersworks', 'id', ['deleted_at' => null, 'selloffer_id' => $offers[count($offers) - 1]]);        
        $I->click('#dataTableVehicleWorks #delButton' . $offer_works[count($offer_works) - 1]); 
        $I->wait(3);
        $I->dontSeeInDatabase('selloffersworks', ['id' => $offer_works[count($offer_works) - 1]]);
    }
}
