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
        $I->amOnPage('/intranet/admin');
        $I->click('Empresas', '.list-group-item');
        $I->see('Company');
        $I->seeCurrentUrlEquals('/intranet/company/list');
        $I->amOnPage('/intranet/admin');
        $I->click('Usuarios', '.list-group-item');        
        $I->seeCurrentUrlEquals('/intranet/users/list');
        $I->amOnPage('/intranet/admin');
        $I->click('Comerciales', '.list-group-item');        
        $I->seeCurrentUrlEquals('/intranet/sellers/list');
        $I->amOnPage('/intranet/admin');
        $I->click('Entidades Bancarias', '.list-group-item');        
        $I->seeCurrentUrlEquals('/intranet/banks/list');
        $I->amOnPage('/intranet/admin');
        $I->click('Financieras', '.list-group-item');        
        $I->seeCurrentUrlEquals('/intranet/finance/list');
        $I->amOnPage('/intranet/admin');
        $I->click('Almacenes', '.list-group-item');        
        $I->seeCurrentUrlEquals('/intranet/stores/list');
        $I->amOnPage('/intranet/admin');
        $I->click('Proveedores', '.list-group-item');        
        $I->seeCurrentUrlEquals('/intranet/buys/providers/list');
        $I->amOnPage('/intranet/admin');
        $I->click('Talleres', '.list-group-item');        
        $I->seeCurrentUrlEquals('/intranet/buys/garages/list');
        $I->amOnPage('/intranet/admin');
        $I->click('Vehículos', '.list-group-item');        
        $I->seeCurrentUrlEquals('/intranet/vehicles/list');
        $I->amOnPage('/intranet/admin');
        $I->click('Tipos Vehículo', '.list-group-item');        
        $I->seeCurrentUrlEquals('/intranet/vehicles/vehicleTypes/list');
        $I->amOnPage('/intranet/admin');
        $I->click('Marcas', '.list-group-item');        
        $I->seeCurrentUrlEquals('/intranet/vehicles/brands/list');
        $I->amOnPage('/intranet/admin');
        $I->click('Modelos', '.list-group-item');        
        $I->seeCurrentUrlEquals('/intranet/vehicles/models/list');
        $I->amOnPage('/intranet/admin');
        $I->click('Accesorios', '.list-group-item');        
        $I->seeCurrentUrlEquals('/intranet/vehicles/accesories/list');
        $I->amOnPage('/intranet/admin');
        $I->click('Trabajos', '.list-group-item');        
        $I->seeCurrentUrlEquals('/intranet/vehicles/works/list');
        $I->amOnPage('/intranet/admin');
        $I->click('Recambios', '.list-group-item');        
        $I->seeCurrentUrlEquals('/intranet/buys/supplies/list');
        $I->amOnPage('/intranet/admin');
        $I->click('Ubicaciones', '.list-group-item');        
        $I->seeCurrentUrlEquals('/intranet/locations/list');
        $I->amOnPage('/intranet/admin');
        $I->click('Componentes', '.list-group-item');        
        $I->seeCurrentUrlEquals('/intranet/buys/components/list');
        $I->amOnPage('/intranet/admin');
        $I->click('Fabricantes', '.list-group-item');        
        $I->seeCurrentUrlEquals('/intranet/buys/maders/list');
        $I->amOnPage('/intranet/admin');
        $I->click('Ofertas', '.list-group-item');        
        $I->seeCurrentUrlEquals('/intranet/sells/offers/list');
        $I->amOnPage('/intranet/admin');
        $I->click('Clientes', '.list-group-item');        
        $I->seeCurrentUrlEquals('/intranet/customers/list');
        $I->amOnPage('/intranet/admin');
        $I->click('Tipos Clientes', '.list-group-item');        
        $I->seeCurrentUrlEquals('/intranet/customers/type/list');
        
                
    }
}
