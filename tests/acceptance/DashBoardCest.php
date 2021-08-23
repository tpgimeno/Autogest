<?php 
namespace App\Acceptance\Tests;

use AcceptanceTester;
use FunctionalTester;
use FirstCest;


class DashBoardCest
{
    public function _before(AcceptanceTester $I)
    {
        FirstCest::loginTest($I);        
    }
    // tests
    public function linksTest(FunctionalTester $I)
    {
        $I->amOnPage('/Intranet/admin');
        $I->click('Empresas', '.list-group-item');
        $I->see('Company');
        $I->seeCurrentUrlEquals('/Intranet/company/list');
        $I->amOnPage('/Intranet/admin');
        $I->click('Usuarios', '.list-group-item');        
        $I->seeCurrentUrlEquals('/Intranet/users/list');
        $I->amOnPage('/Intranet/admin');
        $I->click('Comerciales', '.list-group-item');        
        $I->seeCurrentUrlEquals('/Intranet/sellers/list');
        $I->amOnPage('/Intranet/admin');
        $I->click('Entidades Bancarias', '.list-group-item');        
        $I->seeCurrentUrlEquals('/Intranet/banks/list');
        $I->amOnPage('/Intranet/admin');
        $I->click('Financieras', '.list-group-item');        
        $I->seeCurrentUrlEquals('/Intranet/finance/list');
        $I->amOnPage('/Intranet/admin');
        $I->click('Almacenes', '.list-group-item');        
        $I->seeCurrentUrlEquals('/Intranet/stores/list');
        $I->amOnPage('/Intranet/admin');
        $I->click('Proveedores', '.list-group-item');        
        $I->seeCurrentUrlEquals('/Intranet/buys/providers/list');
        $I->amOnPage('/Intranet/admin');
        $I->click('Talleres', '.list-group-item');        
        $I->seeCurrentUrlEquals('/Intranet/buys/garages/list');
        $I->amOnPage('/Intranet/admin');
        $I->click('Vehículos', '.list-group-item');        
        $I->seeCurrentUrlEquals('/Intranet/vehicles/list');
        $I->amOnPage('/Intranet/admin');
        $I->click('Tipos Vehículo', '.list-group-item');        
        $I->seeCurrentUrlEquals('/Intranet/vehicles/vehicleTypes/list');
        $I->amOnPage('/Intranet/admin');
        $I->click('Marcas', '.list-group-item');        
        $I->seeCurrentUrlEquals('/Intranet/vehicles/brands/list');
        $I->amOnPage('/Intranet/admin');
        $I->click('Modelos', '.list-group-item');        
        $I->seeCurrentUrlEquals('/Intranet/vehicles/models/list');
        $I->amOnPage('/Intranet/admin');
        $I->click('Accesorios', '.list-group-item');        
        $I->seeCurrentUrlEquals('/Intranet/vehicles/accesories/list');
        $I->amOnPage('/Intranet/admin');
        $I->click('Trabajos', '.list-group-item');        
        $I->seeCurrentUrlEquals('/Intranet/vehicles/works/list');
        $I->amOnPage('/Intranet/admin');
        $I->click('Recambios', '.list-group-item');        
        $I->seeCurrentUrlEquals('/Intranet/buys/supplies/list');
        $I->amOnPage('/Intranet/admin');
        $I->click('Ubicaciones', '.list-group-item');        
        $I->seeCurrentUrlEquals('/Intranet/locations/list');
        $I->amOnPage('/Intranet/admin');
        $I->click('Componentes', '.list-group-item');        
        $I->seeCurrentUrlEquals('/Intranet/buys/components/list');
        $I->amOnPage('/Intranet/admin');
        $I->click('Fabricantes', '.list-group-item');        
        $I->seeCurrentUrlEquals('/Intranet/buys/maders/list');
        $I->amOnPage('/Intranet/admin');
        $I->click('Ofertas', '.list-group-item');        
        $I->seeCurrentUrlEquals('/Intranet/sells/offers/list');
        $I->amOnPage('/Intranet/admin');
        $I->click('Clientes', '.list-group-item');        
        $I->seeCurrentUrlEquals('/Intranet/customers/list');
        $I->amOnPage('/Intranet/admin');
        $I->click('Tipos Clientes', '.list-group-item');        
        $I->seeCurrentUrlEquals('/Intranet/customers/type/list');
        
                
    }
}
