<?php

namespace App\Routes;

use Aura\Router\RouterContainer;

class SellRoutes
{
    public function getSellRoutes()
    {
        $routerContainer = new RouterContainer();
        $map = $routerContainer->getMap();

        /*
         * SELLS ROUTES
         */

        // CUSTOMER

        $map->get('customerForm', '/Intranet/customers/form', [
            'App\Controllers\Sales\CustomerController',
            'getCustomerDataAction'
        ]);
        $map->get('customerList', '/Intranet/customers/list', [
            'App\Controllers\Sales\CustomerController',
            'getIndexAction'
        ]);        
        $map->post('saveCustomer', '/Intranet/customers/save', [
            'App\Controllers\Sales\CustomerController',
            'getCustomerDataAction'
        ]);
        $map->get('deleteCustomer', '/Intranet/customers/delete', [        
            'App\Controllers\Sales\CustomerController',
            'deleteAction'   
        ]);

        //CUSTOMER TYPES

        $map->get('customerTypeForm', '/Intranet/customers/type/form', [
            'App\Controllers\Sales\CustomerTypesController',
            'getCustomerTypesDataAction'
        ]);
        $map->get('customerTypeList', '/Intranet/customers/type/list', [
            'App\Controllers\Sales\CustomerTypesController',
            'getIndexAction'
        ]);        
        $map->post('saveCustomerType', '/Intranet/customers/type/save', [
            'App\Controllers\Sales\CustomerTypesController',
            'getCustomerTypesDataAction'
        ]);
        $map->get('deleteCustomerType', '/Intranet/customers/type/delete', [        
            'App\Controllers\Sales\CustomerTypesController',
            'deleteAction'   
        ]);

        //SELLOFFERS

        $map->get('sellOffersForm', '/Intranet/sales/offers/form', [
            'App\Controllers\Sales\SellOffersController',
            'getSellOffersDataAction'
        ]);
        $map->get('sellOffersList', '/Intranet/sales/offers/list', [
            'App\Controllers\Sales\SellOffersController',
            'getIndexAction'
        ]);      
        $map->post('saveSellOffers', '/Intranet/sales/offers/save', [
            'App\Controllers\Sales\SellOffersController',
            'getSellOffersDataAction'
        ]);
        $map->post('deleteSellOffers', '/Intranet/sales/offers/delete', [        
            'App\Controllers\Sales\SellOffersController',
            'deleteAction'   
        ]);
        $map->post('getModelsByBrand', '/Intranet/sales/offers/brands/get', [
            'App\Controllers\Sales\SellOffersController',
            'getSellOffersModelsbyBrand'
        ]);
        $map->post('getVehiclesByModel', '/Intranet/sales/offers/vehicles/get', [
            'App\Controllers\Sales\SellOffersController',
            'getSellOffersVehiclesByModel'
        ]);
        
        $map->post('searchCustomerSellOffers', '/Intranet/sales/offers/customer/search', [
            'App\Controllers\Sales\SellOffersController',
            'searchCustomerSellOfferAction'
        ]);
        $map->get('selectCustomerSellOffers', '/Intranet/sales/offers/customer/select', [
            'App\Controllers\Sales\SellOffersController',
            'selectCustomerSellOfferAction'
        ]);
        $map->post('searchVehicleSellOffers', '/Intranet/sales/offers/vehicle/search', [
            'App\Controllers\Sales\SellOffersController',
            'searchVehicleSellOfferAction'
        ]);
        $map->get('selectVehicleSellOffers', '/Intranet/sales/offers/vehicle/select', [
            'App\Controllers\Sales\SellOffersController',
            'selectVehicleSellOfferAction'
        ]);
        $map->post('searchComponentsSellOffers', '/Intranet/sales/offers/components/search', [
            'App\Controllers\Sales\SellOffersController',
            'searchComponentsSellOffersAction'
        ]);
        $map->get('selectComponentsSellOffers', '/Intranet/sales/offers/components/select', [
            'App\Controllers\Sales\SellOffersController',
            'selectComponentsSellOffersAction'
        ]);
        $map->post('addComponentsSellOffers', '/Intranet/sales/offers/components/add', [
            'App\Controllers\Sales\SellOffersController',
            'addComponentsSellOffersAction'
        ]);
        $map->get('editComponentsSellOffers', '/Intranet/sales/offers/components/edit', [
            'App\Controllers\Sales\SellOffersController',
            'editComponentsSellOffersAction'
        ]);
        $map->get('delComponentsSellOffers', '/Intranet/sales/offers/components/del', [
            'App\Controllers\Sales\SellOffersController',
            'delComponentsSellOffersAction'
        ]);
        $map->post('searchSuppliesSellOffers', '/Intranet/sales/offers/supplies/search', [
            'App\Controllers\Sales\SellOffersController',
            'searchSuppliesSellOffersAction'
        ]);
        $map->get('selectSuppliesSellOffers', '/Intranet/sales/offers/supplies/select', [
            'App\Controllers\Sales\SellOffersController',
            'selectSuppliesSellOffersAction'
        ]);
        $map->post('addSuppliesSellOffers', '/Intranet/sales/offers/supplies/add', [
            'App\Controllers\Sales\SellOffersController',
            'addSuppliesSellOffersAction'
        ]);
        $map->get('editSuppliesSellOffers', '/Intranet/sales/offers/supplies/edit', [
            'App\Controllers\Sales\SellOffersController',
            'editSuppliesSellOffersAction'
        ]);
        $map->get('delSuppliesSellOffers', '/Intranet/sales/offers/supplies/del', [
            'App\Controllers\Sales\SellOffersController',
            'delSuppliesSellOffersAction'
        ]);
        $map->post('searchWorksSellOffers', '/Intranet/sales/offers/works/search', [
            'App\Controllers\Sales\SellOffersController',
            'searchWorksSellOffersAction'
        ]);
        $map->get('selectWorksSellOffers', '/Intranet/sales/offers/works/select', [
            'App\Controllers\Sales\SellOffersController',
            'selectWorksSellOffersAction'
        ]);
        $map->post('addWorksSellOffers', '/Intranet/sales/offers/works/add', [
            'App\Controllers\Sales\SellOffersController',
            'addWorksSellOffersAction'
        ]);
        $map->get('editWorksSellOffers', '/Intranet/sales/offers/works/edit', [
            'App\Controllers\Sales\SellOffersController',
            'editWorksSellOffersAction'
        ]);
        $map->get('delWorksSellOffers', '/Intranet/sales/offers/works/del', [
            'App\Controllers\Sales\SellOffersController',
            'delWorksSellOffersAction'
        ]);
        
        
        

        //SELL DELIVERIES

        $map->get('sellDeliveriesForm', '/Intranet/sales/sellDeliveries/form', [
            'App\Controllers\Sales\SellDeliveriesController',
            'getSellDeliveriesDataAction'
        ]);
        $map->get('sellDeliveriesList', '/Intranet/sales/sellDeliveries/list', [
            'App\Controllers\Sales\SellDeliveriesController',
            'getIndexAction'
        ]);
        $map->post('searchSellDeliveries', '/Intranet/sales/sellDeliveries/search', [
            'App\Controllers\Sales\SellDeliveriesController',
            'searchSellDeliveriesAction'
        ]);
        $map->post('saveSellDeliveries', '/Intranet/sales/sellDeliveries/save', [
            'App\Controllers\Sales\SellDeliveriesController',
            'getSellDeliveriesDataAction'
        ]);
        $map->get('deleteSellDeliveries', '/Intranet/sales/sellDeliveries/delete', [        
            'App\Controllers\Sales\SellDeliveriesController',
            'deleteAction'  
        ]);

        //SELL INVOICES

        $map->get('sellInvoicesForm', '/Intranet/sales/invoices/form', [
            'App\Controllers\Sales\SellInvoicesController',
            'getSellInvoicesDataAction'
        ]);
        $map->get('sellInvoicesList', '/Intranet/sales/invoices/list', [
            'App\Controllers\Sales\SellInvoicesController',
            'getIndexAction'
        ]);
        $map->post('searchSellInvoices', '/Intranet/sales/invoices/search', [
            'App\Controllers\Sales\SellInvoicesController',
            'searchSellInvoicesAction'
        ]);
        $map->post('saveSellInvoices', '/Intranet/sales/invoices/save', [
            'App\Controllers\Sales\SellInvoicesController',
            'getSellInvoicesDataAction'
        ]);
        $map->get('deleteSellInvoices', '/Intranet/sales/invoices/delete', [        
            'App\Controllers\Sales\SellInvoicesController',
            'deleteAction'  
        ]);        

        //SELLERS

        $map->get('sellerList', '/Intranet/sellers/form', [
            'App\Controllers\Sales\SellersController',
            'getSellersDataAction'
        ]);
        $map->get('sellerForm', '/Intranet/sellers/list', [
            'App\Controllers\Sales\SellersController',
            'getIndexAction'
        ]);
        $map->post('saveSeller', '/Intranet/sellers/save', [
            'App\Controllers\Sales\SellersController',
            'getSellersDataAction'
        ]);
        $map->get('sellerDelete', '/Intranet/sellers/delete', [
            'App\Controllers\Sales\SellersController',
            'deleteAction'
        ]);
        //RECIPES

        $map->get('repicesList', '/Intranet/sales/repices/form', [
            'App\Controllers\Sales\RecipesController',
            'getRecipesDataAction'
        ]);
        $map->get('repicesForm', '/Intranet/sales/repices/list', [
            'App\Controllers\Sales\RecipesController',
            'getIndexAction'
        ]);
        $map->post('saveRecipes', '/Intranet/sales/repices/save', [
            'App\Controllers\Sales\RecipesController',
            'getRecipesDataAction'
        ]);
        $map->get('repicesDelete', '/Intranet/sales/repices/delete', [
            'App\Controllers\Sales\RecipesController',
            'deleteAction'
        ]);
        return $routerContainer;
    }
}


