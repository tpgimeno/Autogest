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
            'App\Controllers\Sells\CustomerController',
            'getCustomerDataAction'
        ]);
        $map->get('customerList', '/Intranet/customers/list', [
            'App\Controllers\Sells\CustomerController',
            'getIndexAction'
        ]);
        $map->post('searchCustomer', '/Intranet/customers/search', [
            'App\Controllers\Sells\CustomerController',
            'searchCustomerAction'
        ]);
        $map->post('saveCustomer', '/Intranet/customers/save', [
            'App\Controllers\Sells\CustomerController',
            'getCustomerDataAction'
        ]);
        $map->get('deleteCustomer', '/Intranet/customers/delete', [        
            'App\Controllers\Sells\CustomerController',
            'deleteAction'   
        ]);

        //CUSTOMER TYPES

        $map->get('customerTypeForm', '/Intranet/customers/type/form', [
            'App\Controllers\Sells\CustomerTypesController',
            'getCustomerTypesDataAction'
        ]);
        $map->get('customerTypeList', '/Intranet/customers/type/list', [
            'App\Controllers\Sells\CustomerTypesController',
            'getIndexAction'
        ]);
        $map->post('searchCustomerType', '/Intranet/customers/type/search', [
            'App\Controllers\Sells\CustomerTypesController',
            'searchCustomerTypeAction'
        ]);
        $map->post('saveCustomerType', '/Intranet/customers/type/save', [
            'App\Controllers\Sells\CustomerTypesController',
            'getCustomerTypesDataAction'
        ]);
        $map->get('deleteCustomerType', '/Intranet/customers/type/delete', [        
            'App\Controllers\Sells\CustomerTypesController',
            'deleteAction'   
        ]);

        //SELLOFFERS

        $map->get('sellOffersForm', '/Intranet/sells/offers/form', [
            'App\Controllers\Sells\SellOffersController',
            'getSellOffersDataAction'
        ]);
        $map->get('sellOffersList', '/Intranet/sells/offers/list', [
            'App\Controllers\Sells\SellOffersController',
            'getIndexAction'
        ]);
        $map->post('searchSellOffers', '/Intranet/sells/offers/search', [
            'App\Controllers\Sells\SellOffersController',
            'searchSellOffersAction'
        ]);
        $map->post('searchCustomerSellOffers', '/Intranet/sells/offers/customer/search', [
            'App\Controllers\Sells\SellOffersController',
            'searchCustomerSellOfferAction'
        ]);
        $map->get('selectCustomerSellOffers', '/Intranet/sells/offers/customer/select', [
            'App\Controllers\Sells\SellOffersController',
            'selectCustomerSellOfferAction'
        ]);
        $map->post('searchVehicleSellOffers', '/Intranet/sells/offers/vehicle/search', [
            'App\Controllers\Sells\SellOffersController',
            'searchVehicleSellOfferAction'
        ]);
        $map->get('selectVehicleSellOffers', '/Intranet/sells/offers/vehicle/select', [
            'App\Controllers\Sells\SellOffersController',
            'selectVehicleSellOfferAction'
        ]);
        $map->post('searchComponentsSellOffers', '/Intranet/sells/offers/components/search', [
            'App\Controllers\Sells\SellOffersController',
            'searchComponentsSellOffersAction'
        ]);
        $map->get('selectComponentsSellOffers', '/Intranet/sells/offers/components/select', [
            'App\Controllers\Sells\SellOffersController',
            'selectComponentsSellOffersAction'
        ]);
        $map->post('addComponentsSellOffers', '/Intranet/sells/offers/components/add', [
            'App\Controllers\Sells\SellOffersController',
            'addComponentsSellOffersAction'
        ]);
        $map->get('editComponentsSellOffers', '/Intranet/sells/offers/components/edit', [
            'App\Controllers\Sells\SellOffersController',
            'editComponentsSellOffersAction'
        ]);
        $map->get('delComponentsSellOffers', '/Intranet/sells/offers/components/del', [
            'App\Controllers\Sells\SellOffersController',
            'delComponentsSellOffersAction'
        ]);
        $map->post('searchSuppliesSellOffers', '/Intranet/sells/offers/supplies/search', [
            'App\Controllers\Sells\SellOffersController',
            'searchSuppliesSellOffersAction'
        ]);
        $map->get('selectSuppliesSellOffers', '/Intranet/sells/offers/supplies/select', [
            'App\Controllers\Sells\SellOffersController',
            'selectSuppliesSellOffersAction'
        ]);
        $map->post('addSuppliesSellOffers', '/Intranet/sells/offers/supplies/add', [
            'App\Controllers\Sells\SellOffersController',
            'addSuppliesSellOffersAction'
        ]);
        $map->get('editSuppliesSellOffers', '/Intranet/sells/offers/supplies/edit', [
            'App\Controllers\Sells\SellOffersController',
            'editSuppliesSellOffersAction'
        ]);
        $map->get('delSuppliesSellOffers', '/Intranet/sells/offers/supplies/del', [
            'App\Controllers\Sells\SellOffersController',
            'delSuppliesSellOffersAction'
        ]);
        $map->post('searchWorksSellOffers', '/Intranet/sells/offers/works/search', [
            'App\Controllers\Sells\SellOffersController',
            'searchWorksSellOffersAction'
        ]);
        $map->get('selectWorksSellOffers', '/Intranet/sells/offers/works/select', [
            'App\Controllers\Sells\SellOffersController',
            'selectWorksSellOffersAction'
        ]);
        $map->post('addWorksSellOffers', '/Intranet/sells/offers/works/add', [
            'App\Controllers\Sells\SellOffersController',
            'addWorksSellOffersAction'
        ]);
        $map->get('editWorksSellOffers', '/Intranet/sells/offers/works/edit', [
            'App\Controllers\Sells\SellOffersController',
            'editWorksSellOffersAction'
        ]);
        $map->get('delWorksSellOffers', '/Intranet/sells/offers/works/del', [
            'App\Controllers\Sells\SellOffersController',
            'delWorksSellOffersAction'
        ]);
        $map->post('saveSellOffers', '/Intranet/sells/offers/save', [
            'App\Controllers\Sells\SellOffersController',
            'getSellOffersDataAction'
        ]);
        $map->get('deleteSellOffers', '/Intranet/sells/offers/delete', [        
            'App\Controllers\Sells\SellOffersController',
            'deleteAction'   
        ]);

        //SELL DELIVERIES

        $map->get('sellDeliveriesForm', '/Intranet/sells/sellDeliveries/form', [
            'App\Controllers\Sells\SellDeliveriesController',
            'getSellDeliveriesDataAction'
        ]);
        $map->get('sellDeliveriesList', '/Intranet/sells/sellDeliveries/list', [
            'App\Controllers\Sells\SellDeliveriesController',
            'getIndexAction'
        ]);
        $map->post('searchSellDeliveries', '/Intranet/sells/sellDeliveries/search', [
            'App\Controllers\Sells\SellDeliveriesController',
            'searchSellDeliveriesAction'
        ]);
        $map->post('saveSellDeliveries', '/Intranet/sells/sellDeliveries/save', [
            'App\Controllers\Sells\SellDeliveriesController',
            'getSellDeliveriesDataAction'
        ]);
        $map->get('deleteSellDeliveries', '/Intranet/sells/sellDeliveries/delete', [        
            'App\Controllers\Sells\SellDeliveriesController',
            'deleteAction'  
        ]);

        //SELL INVOICES

        $map->get('sellInvoicesForm', '/Intranet/sells/invoices/form', [
            'App\Controllers\Sells\SellInvoicesController',
            'getSellInvoicesDataAction'
        ]);
        $map->get('sellInvoicesList', '/Intranet/sells/invoices/list', [
            'App\Controllers\Sells\SellInvoicesController',
            'getIndexAction'
        ]);
        $map->post('searchSellInvoices', '/Intranet/sells/invoices/search', [
            'App\Controllers\Sells\SellInvoicesController',
            'searchSellInvoicesAction'
        ]);
        $map->post('saveSellInvoices', '/Intranet/sells/invoices/save', [
            'App\Controllers\Sells\SellInvoicesController',
            'getSellInvoicesDataAction'
        ]);
        $map->get('deleteSellInvoices', '/Intranet/sells/invoices/delete', [        
            'App\Controllers\Sells\SellInvoicesController',
            'deleteAction'  
        ]);        

        //SELLERS

        $map->get('sellerList', '/Intranet/sellers/form', [
            'App\Controllers\Entitys\SellersController',
            'getSellersDataAction'
        ]);
        $map->get('sellerForm', '/Intranet/sellers/list', [
            'App\Controllers\Entitys\SellersController',
            'getIndexAction'
        ]);
        $map->post('saveSeller', '/Intranet/sellers/save', [
            'App\Controllers\Entitys\SellersController',
            'getSellersDataAction'
        ]);
        $map->get('sellerDelete', '/Intranet/sellers/delete', [
            'App\Controllers\Entitys\SellersController',
            'deleteAction'
        ]);
        //RECIPES

        $map->get('repicesList', '/Intranet/sells/repices/form', [
            'App\Controllers\Sells\RecipesController',
            'getRecipesDataAction'
        ]);
        $map->get('repicesForm', '/Intranet/sells/repices/list', [
            'App\Controllers\Sells\RecipesController',
            'getIndexAction'
        ]);
        $map->post('saveRecipes', '/Intranet/sells/repices/save', [
            'App\Controllers\Sells\RecipesController',
            'getRecipesDataAction'
        ]);
        $map->get('repicesDelete', '/Intranet/sells/repices/delete', [
            'App\Controllers\Sells\RecipesController',
            'deleteAction'
        ]);
        return $routerContainer;
    }
}


