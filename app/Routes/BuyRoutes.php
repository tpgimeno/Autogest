<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace App\Routes;

use Aura\Router\RouterContainer;

/**
 * Description of BuyRoutes
 *
 * @author tony
 */
class BuyRoutes 
{
    public function getBuyRoutes()
    {
        $routerContainer = new RouterContainer();
        $map = $routerContainer->getMap();
        
        /*
        * BUYS ROUTES
        */

       //VEHICLE

       $map->get('vehicleForm', '/Intranet/vehicles/form', [
           'App\Controllers\Vehicle\VehicleController',
           'getVehicleDataAction'
       ]);
       $map->get('vehicleList', '/Intranet/vehicles/list', [
           'App\Controllers\Vehicle\VehicleController',
           'getIndexAction'
       ]);
       $map->post('searchVehicle', '/Intranet/vehicles/search', [
           'App\Controllers\Vehicle\VehicleController',
           'searchVehicleAction'
       ]);
       $map->post('importVehicles', '/Intranet/vehicles/import', [
           'App\Controllers\Vehicle\VehicleController',
           'importVehiclesExcel'
       ]);
       $map->post('saveVehicle', '/Intranet/vehicles/save', [
           'App\Controllers\Vehicle\VehicleController',
           'getVehicleDataAction'
       ]);
       $map->get('deleteVehicle', '/Intranet/vehicles/delete', [        
           'App\Controllers\Vehicle\VehicleController',
           'deleteAction'   
       ]);
       $map->post('reloadModels', '/Intranet/vehicles/models/reload', [
           'App\Controllers\Vehicle\VehicleController',
           'reloadModelsAction'
       ]);
       $map->post('reloadLocations', '/Intranet/vehicles/locations/reload', [
           'App\Controllers\Vehicle\VehicleController',
           'reloadLocationsAction'
       ]);

       //ACCESORIES

       $map->get('accesoryForm', '/Intranet/vehicles/accesories/form', [
           'App\Controllers\Vehicle\AccesoriesController',
           'getAccesoryDataAction'
       ]);
       $map->get('accesoryList', '/Intranet/vehicles/accesories/list', [
           'App\Controllers\Vehicle\AccesoriesController',
           'getIndexAction'
       ]);
       $map->post('searchAccesory', '/Intranet/vehicles/accesories/search', [
           'App\Controllers\Vehicle\AccesoriesController',
           'searchAccesoryAction'
       ]);
       $map->post('saveAccesory', '/Intranet/vehicles/accesories/save', [
           'App\Controllers\Vehicle\AccesoriesController',
           'getAccesoryDataAction'
       ]);
       $map->get('deleteAccesory', '/Intranet/vehicles/accesories/delete', [        
           'App\Controllers\Vehicle\AccesoriesController',
           'deleteAction'   
       ]);
       $map->post('getVehicleAccesory', '/Intranet/vehicles/accesories/get', [        
           'App\Controllers\Vehicle\VehicleController',
           'getAccesoryAction'   
       ]);
       $map->post('addVehicleAccesory', '/Intranet/vehicles/accesories/add', [        
           'App\Controllers\Vehicle\VehicleController',
           'addAccesoryAction'   
       ]);
       $map->post('deleteVehicleAccesory', '/Intranet/vehicles/accesories/del', [        
           'App\Controllers\Vehicle\VehicleController',
           'deleteAccesoryAction'   
       ]);

       //BRANDS

       $map->get('brandForm', '/Intranet/vehicles/brands/form', [
           'App\Controllers\Vehicle\BrandController',
           'getBrandDataAction'
       ]);
       $map->get('brandList', '/Intranet/vehicles/brands/list', [
           'App\Controllers\Vehicle\BrandController',
           'getIndexAction'
       ]);
       $map->post('searchBrand', '/Intranet/vehicles/brands/search', [
           'App\Controllers\Vehicle\BrandController',
           'searchBrandAction'
       ]);
       $map->post('saveBrand', '/Intranet/vehicles/brands/save', [
           'App\Controllers\Vehicle\BrandController',
           'getBrandDataAction'
       ]);
       $map->get('deleteBrand', '/Intranet/vehicles/brands/delete', [        
           'App\Controllers\Vehicle\BrandController',
           'deleteAction'   
       ]);

       //MODELS

       $map->get('modelForm', '/Intranet/vehicles/models/form', [
           'App\Controllers\Vehicle\ModelController',
           'getModelDataAction'
       ]);
       $map->get('modelList', '/Intranet/vehicles/models/list', [
           'App\Controllers\Vehicle\ModelController',
           'getIndexAction'
       ]);
       $map->post('searchModel', '/Intranet/vehicles/models/search', [
           'App\Controllers\Vehicle\ModelController',
           'searchModelAction'
       ]);
       $map->post('saveModel', '/Intranet/vehicles/models/save', [
           'App\Controllers\Vehicle\ModelController',
           'getModelDataAction'
       ]);
       $map->get('deleteModel', '/Intranet/vehicles/models/delete', [        
           'App\Controllers\Vehicle\ModelController',
           'deleteAction'   
       ]);
       

       //COMPONENTS

       $map->get('ComponentsForm', '/Intranet/vehicles/components/form', [
           'App\Controllers\Vehicle\ComponentsController',
           'getComponentsDataAction'
       ]);
       $map->get('ComponentsList', '/Intranet/vehicles/components/list', [
           'App\Controllers\Vehicle\ComponentsController',
           'getIndexAction'
       ]);
       $map->post('searchComponents', '/Intranet/vehicles/components/search', [
           'App\Controllers\Vehicle\ComponentsController',
           'searchComponentsAction'
       ]);
       $map->post('saveComponents', '/Intranet/vehicles/components/save', [
           'App\Controllers\Vehicle\ComponentsController',
           'getComponentsDataAction'
       ]);
       $map->get('deleteComponents', '/Intranet/vehicles/components/delete', [        
           'App\Controllers\Vehicle\ComponentsController',
           'deleteAction'   
       ]);

       //SUPPLIES

       $map->get('SuppliesForm', '/Intranet/vehicles/supplies/form', [
           'App\Controllers\Vehicle\SuppliesController',
           'getSuppliesDataAction'
       ]);
       $map->get('SuppliesList', '/Intranet/vehicles/supplies/list', [
           'App\Controllers\Vehicle\SuppliesController',
           'getIndexAction'
       ]);
       $map->post('searchSupplies', '/Intranet/vehicles/supplies/search', [
           'App\Controllers\Vehicle\SuppliesController',
           'searchSuppliesAction'
       ]);
       $map->post('saveSupplies', '/Intranet/vehicles/supplies/save', [
           'App\Controllers\Vehicle\SuppliesController',
           'getSuppliesDataAction'
       ]);
       $map->get('deleteSupplies', '/Intranet/vehicles/supplies/delete', [        
           'App\Controllers\Vehicle\SuppliesController',
           'deleteAction'   
       ]);

       //WORKS

       $map->get('WorksForm', '/Intranet/vehicles/works/form', [
           'App\Controllers\Vehicle\WorksController',
           'getWorkDataAction'
       ]);
       $map->get('WorksList', '/Intranet/vehicles/works/list', [
           'App\Controllers\Vehicle\WorksController',
           'getIndexAction'
       ]);
       $map->post('searchWorks', '/Intranet/vehicles/works/search', [
           'App\Controllers\Vehicle\WorksController',
           'searchWorksAction'
       ]);
       $map->post('saveWorks', '/Intranet/vehicles/works/save', [
           'App\Controllers\Vehicle\WorksController',
           'getWorkDataAction'
       ]);
       $map->get('deleteWorks', '/Intranet/vehicles/works/delete', [        
           'App\Controllers\Vehicle\WorksController',
           'deleteAction'   
       ]);

       //VEHICLES TYPES

       $map->get('vehicleTypesForm', '/Intranet/vehicles/vehicleTypes/form', [
           'App\Controllers\Vehicle\VehicleTypesController',
           'getVehicleTypesDataAction'
       ]);
       $map->get('vehicleTypesList', '/Intranet/vehicles/vehicleTypes/list', [
           'App\Controllers\Vehicle\VehicleTypesController',
           'getIndexAction'
       ]);
       $map->post('searchVehicleTypes', '/Intranet/vehicles/vehicleTypes/search', [
           'App\Controllers\Vehicle\VehicleTypesController',
           'searchVehicleTypesAction'
       ]);
       $map->post('saveVehicleTypes', '/Intranet/vehicles/vehicleTypes/save', [
           'App\Controllers\Vehicle\VehicleTypesController',
           'getVehicleTypesDataAction'
       ]);
       $map->get('deleteVehicleTypes', '/Intranet/vehicles/vehicleTypes/delete', [        
           'App\Controllers\Vehicle\VehicleTypesController',
           'deleteAction'   
       ]);

       //BUY DELIVERIES

       $map->get('buyDeliveriesForm', '/Intranet/buys/buyDeliveries/form', [
           'App\Controllers\Buys\BuyDeliveriesController',
           'getBuyDeliveriesDataAction'
       ]);
       $map->get('buyDeliveriesList', '/Intranet/buys/buyDeliveries/list', [
           'App\Controllers\Buys\BuyDeliveriesController',
           'getIndexAction'
       ]);
       $map->post('searchBuyDeliveries', '/Intranet/buys/buyDeliveries/search', [
           'App\Controllers\Buys\BuyDeliveriesController',
           'searchBuyDeliveriesAction'
       ]);
       $map->post('saveBuyDeliveries', '/Intranet/buys/buyDeliveries/save', [
           'App\Controllers\Buys\BuyDeliveriesController',
           'getBuyDeliveriesDataAction'
       ]);
       $map->get('deleteBuyDeliveries', '/Intranet/buys/buyDeliveries/delete', [        
           'App\Controllers\Buys\BuyDeliveriesController',
           'deleteAction'  
       ]);

       //BUY INVOICES

       $map->get('buyInvoicesForm', '/Intranet/buys/buyInvoices/form', [
           'App\Controllers\Buys\BuyInvoicesController',
           'getBuyInvoicesDataAction'
       ]);
       $map->get('buyInvoicesList', '/Intranet/buys/buyInvoices/list', [
           'App\Controllers\Buys\BuyInvoicesController',
           'getIndexAction'
       ]);
       $map->post('searchBuyInvoices', '/Intranet/buys/buyInvoices/search', [
           'App\Controllers\Buys\BuyInvoicesController',
           'searchBuyInvoicesAction'
       ]);
       $map->post('saveBuyInvoices', '/Intranet/buys/buyInvoices/save', [
           'App\Controllers\Buys\BuyInvoicesController',
           'getBuyInvoicesDataAction'
       ]);
       $map->get('deleteBuyInvoices', '/Intranet/buys/buyInvoices/delete', [        
           'App\Controllers\Buys\BuyInvoicesController',
           'deleteAction'  
       ]);

       //VEHICLE COMPONENTS

       
       $map->post('vehicleComponentsPostSelect', '/Intranet/vehicles/vehicleComponents/set', [
           'App\Controllers\Vehicle\VehicleController',
           'getVehicleComponentsAction'
       ]);
       $map->get('vehicleComponentsGetSelect', '/Intranet/vehicles/vehicleComponents/set', [
           'App\Controllers\Vehicle\VehicleController',
           'getVehicleComponentsAction'
       ]);
       $map->post('vehicleComponentsAdd', '/Intranet/vehicles/vehicleComponents/save', [
           'App\Controllers\Vehicle\VehicleController',
           'addVehicleComponentAction'
       ]);
       $map->post('vehicleComponentsDel', '/Intranet/vehicles/vehicleComponents/del', [        
           'App\Controllers\Vehicle\VehicleController',
           'delVehicleComponentAction'  
       ]);


       //VEHICLE SUPPLIES

       $map->get('vehicleSuppliesSelect', '/Intranet/vehicles/vehicleSupplies/select', [
           'App\Controllers\Vehicle\VehicleController',
           'getVehicleDataAction'
       ]);
       $map->post('vehicleSuppliesAdd', '/Intranet/vehicles/vehicleSupplies/add', [
           'App\Controllers\Vehicle\VehicleController',
           'addSupplyAction'
       ]);
       $map->post('vehiclesSupliesSearch', '/Intranet/vehicles/vehicleSupplies/search', [
           'App\Controllers\Vehicle\VehicleController',
           'searchSupplyAction'
       ]);
       $map->get('vehicleSuppliesEdit', '/Intranet/vehicles/vehicleSupplies/edit', [
           'App\Controllers\Vehicle\VehicleController',
           'editSupplyAction'
       ]);
       $map->get('vehicleSuppliesDel', '/Intranet/vehicles/vehicleSupplies/del', [        
           'App\Controllers\Vehicle\VehicleController',
           'delSupplyAction'  
       ]);

       //PROVIDERS

       $map->get('providorsList', '/Intranet/buys/providors/form', [
           'App\Controllers\Buys\ProvidorsController',
           'getProvidorDataAction'
       ]);
       $map->get('providorsForm', '/Intranet/buys/providors/list', [
           'App\Controllers\Buys\ProvidorsController',
           'getIndexAction'
       ]);
       
       $map->post('saveProvidor', '/Intranet/buys/providors/save', [
           'App\Controllers\Buys\ProvidorsController',
           'getProvidorDataAction'
       ]);
       $map->get('providorsDelete', '/Intranet/buys/providors/delete', [
           'App\Controllers\Buys\ProvidorsController',
           'deleteAction'
       ]);       

       //GARAGE ORDERS

       $map->get('ordersList', '/Intranet/buys/orders/form', [
           'App\Controllers\Garages\GarageOrdersController',
           'getOrderDataAction'
       ]);
       $map->get('ordersForm', '/Intranet/buys/orders/list', [
           'App\Controllers\Garages\GarageOrdersController',
           'getIndexAction'
       ]);
       $map->post('saveWork', '/Intranet/buys/orders/form/work/save', [
           'App\Controllers\Garages\GarageOrdersController',
           'getOrderDataAction'
       ]);
       $map->post('saveOrder', '/Intranet/buys/orders/save', [
           'App\Controllers\Garages\GarageOrdersController',
           'getOrderDataAction'
       ]);
       $map->get('ordersDelete', '/Intranet/buys/orders/delete', [
           'App\Controllers\Garages\GarageOrdersController',
           'deleteAction'
       ]);
       return $routerContainer;
    }
    
}
