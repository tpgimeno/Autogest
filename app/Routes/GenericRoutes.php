<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace App\Routes;

use Aura\Router\RouterContainer;

/**
 * Description of GenericRoutes
 *
 * @author tony
 */
class GenericRoutes {
    public function getGenericRoutes()
    {
        $routerContainer = new RouterContainer();
        $map = $routerContainer->getMap();        
        /*
        * GENERIC ROUTES
        */
       $map->get('index', '/Intranet/', [
           'App\Controllers\IndexController',
           'indexAction'    
       ]);
       $map->post('login', '/Intranet/login', [
           'App\Controllers\AuthController',
           'postLogin'

       ]);
       $map->post('logout', '/Intranet/logout', [
           'App\Controllers\AuthController',
           'getLogout'    
       ]);
       $map->get('dashboard', '/Intranet/admin', [
           'App\Controllers\AdminController',
           'getDashBoardAction'
       ]);

       //COMPANY

       $map->post('companyFormGet', '/Intranet/company/form', [
           'App\Controllers\Entitys\CompanyController',
           'getCompanyDataAction'
       ]);
       $map->get('companyFormPost', '/Intranet/company/form', [
           'App\Controllers\Entitys\CompanyController',
           'getCompanyDataAction'
       ]);
       $map->get('companyList', '/Intranet/company/list', [
           'App\Controllers\Entitys\CompanyController',
           'getIndexAction'
       ]);
       $map->post('searchCompany', '/Intranet/company/search', [
           'App\Controllers\Entitys\CompanyController',
           'searchCompanyAction'
       ]);
       $map->post('saveCompany', '/Intranet/company/save', [
           'App\Controllers\Entitys\CompanyController',
           'getCompanyDataAction'
       ]);
       $map->get('deleteCompany', '/Intranet/company/delete', [        
           'App\Controllers\Entitys\CompanyController',
           'deleteAction'  
       ]);

       //USERS

       $map->get('userList', '/Intranet/users/form', [
           'App\Controllers\Entitys\UsersController',
           'getAddUserAction'
       ]);
       $map->get('userForm', '/Intranet/users/list', [
           'App\Controllers\Entitys\UsersController',
           'getIndexUsers'
       ]);
       $map->post('saveUser', '/Intranet/users/form', [
           'App\Controllers\Entitys\UsersController',
           'getAddUserAction'
       ]);
       $map->get('userDelete', '/Intranet/users/delete', [
           'App\Controllers\Entitys\UsersController',
           'deleteAction'
       ]);

       //BANKS

       $map->get('banksList', '/Intranet/banks/form', [
           'App\Controllers\Entitys\BanksController',
           'getBankDataAction'
       ]);
       $map->get('bankForm', '/Intranet/banks/list', [
           'App\Controllers\Entitys\BanksController',
           'getIndexAction'    
       ]);
       $map->post('searchBank', '/Intranet/banks/search', [
           'App\Controllers\Entitys\BanksController',
           'searchBanksAction' 
       ]);
       $map->post('saveBank', '/Intranet/banks/save', [
           'App\Controllers\Entitys\BanksController',
           'getBankDataAction'    
       ]);
       $map->get('bankDelete', '/Intranet/banks/delete', [
           'App\Controllers\Entitys\BanksController',
           'deleteAction'    
       ]);
       
        //ACCOUNTS

       $map->get('accountsList', '/Intranet/accounts/form', [
           'App\Controllers\Entitys\AccountController',
           'getAccountDataAction'
       ]);
       $map->get('accountForm', '/Intranet/accounts/list', [
           'App\Controllers\Entitys\AccountController',
           'getIndexAction'    
       ]);
       $map->post('searchAccount', '/Intranet/accounts/search', [
           'App\Controllers\Entitys\AccountController',
           'searchAccountsAction' 
       ]);
       $map->post('saveAccount', '/Intranet/accounts/save', [
           'App\Controllers\Entitys\AccountController',
           'getAccountDataAction'    
       ]);
       $map->get('accountDelete', '/Intranet/accounts/delete', [
           'App\Controllers\Entitys\AccountController',
           'deleteAction'    
       ]);

       //FINANCE

       $map->get('financeList', '/Intranet/finance/form', [
           'App\Controllers\Entitys\FinanceController',
           'getFinanceDataAction'
       ]);
       $map->get('financeForm', '/Intranet/finance/list', [
           'App\Controllers\Entitys\FinanceController',
           'getIndexAction'    
       ]);
       $map->post('saveFinance', '/Intranet/finance/save', [
           'App\Controllers\Entitys\FinanceController',
           'getFinanceDataAction'
       ]);
       $map->get('financeDelete', '/Intranet/finance/delete', [
           'App\Controllers\Entitys\FinanceController',
           'deleteAction'    
       ]);

       //STORES

       $map->get('storesList', '/Intranet/stores/form', [
           'App\Controllers\Entitys\StoreController',
           'getStoreDataAction'
       ]);
       $map->get('storesForm', '/Intranet/stores/list', [
           'App\Controllers\Entitys\StoreController',
           'getIndexAction'    
       ]);
       $map->post('searchStore', '/Intranet/stores/search', [
           'App\Controllers\Entitys\StoreController',
           'searchStoreAction' 
       ]);
       $map->post('saveStore', '/Intranet/stores/save', [
           'App\Controllers\Entitys\StoreController',
           'getStoreDataAction' 
       ]);
       $map->get('storesDelete', '/Intranet/stores/delete', [
           'App\Controllers\Entitys\StoreController',
           'deleteAction'
       ]);

       //LOCATIONS

       $map->get('locationsList', '/Intranet/locations/form', [
           'App\Controllers\Entitys\LocationController',
           'getLocationDataAction'
       ]);
       $map->get('locationsForm', '/Intranet/locations/list', [
           'App\Controllers\Entitys\LocationController',
           'getIndexAction'    
       ]);
       $map->post('searchLocations', '/Intranet/locations/search', [
           'App\Controllers\Entitys\LocationController',
           'searchLocationAction'
       ]);
       $map->post('saveLocation', '/Intranet/locations/save', [
           'App\Controllers\Entitys\LocationController',
           'getLocationDataAction' 
       ]);
       $map->get('locationsDelete', '/Intranet/locations/delete', [
           'App\Controllers\Entitys\LocationController',
           'deleteAction'
       ]);

       //GARAGES 

       $map->get('garagesList', '/Intranet/garages/form', [
           'App\Controllers\Garages\GaragesController',
           'getGarageDataAction'
       ]);
       $map->get('garagesForm', '/Intranet/garages/list', [
           'App\Controllers\Garages\GaragesController',
           'getIndexAction'
       ]);
       $map->post('searchGarage', '/Intranet/garages/search', [
           'App\Controllers\Garages\GaragesController',
           'searchGarageAction'
       ]);
       $map->post('saveGarage', '/Intranet/garages/save', [
           'App\Controllers\Garages\GaragesController',
           'getGarageDataAction'
       ]);
       $map->get('garagesDelete', '/Intranet/garages/delete', [
           'App\Controllers\Garages\GaragesController',
           'deleteAction'
       ]);
       
       //MADERS

       $map->get('madersList', '/Intranet/maders/form', [
           'App\Controllers\Entitys\MadersController',
           'getMaderDataAction'
       ]);
       $map->get('madersForm', '/Intranet/maders/list', [
           'App\Controllers\Entitys\MadersController',
           'getIndexAction'    
       ]);
       $map->post('saveMader', '/Intranet/maders/save', [
           'App\Controllers\Entitys\MadersController',
           'getMaderDataAction' 
       ]);
       $map->get('madersDelete', '/Intranet/maders/delete', [
           'App\Controllers\Entitys\MadersController',
           'deleteAction'
       ]);

       //PAYMENT-WAYS

       $map->get('paymentWaysList', '/Intranet/paymentWays/form', [
           'App\Controllers\Entitys\PaymentWaysController',
           'getPaymentWaysDataAction'
       ]);
       $map->get('paymentWaysForm', '/Intranet/paymentWays/list', [
           'App\Controllers\Entitys\PaymentWaysController',
           'getIndexAction'
       ]);
       $map->post('savePaymentWays', '/Intranet/paymentWays/save', [
           'App\Controllers\Entitys\PaymentWaysController',
           'getPaymentWaysDataAction'
       ]);
       $map->get('paymentWaysDelete', '/Intranet/paymentWays/delete', [
           'App\Controllers\Entitys\PaymentWaysController',
           'deleteAction'
       ]);

       //TAXES

       $map->get('taxesList', '/Intranet/taxes/form', [
           'App\Controllers\Entitys\TaxesController',
           'getTaxesDataAction'
       ]);
       $map->get('taxesForm', '/Intranet/taxes/list', [
           'App\Controllers\Entitys\TaxesController',
           'getIndexAction'
       ]);
       $map->post('saveTaxesWays', '/Intranet/taxes/save', [
           'App\Controllers\Entitys\TaxesController',
           'getTaxesDataAction'
       ]);
       $map->get('taxesDelete', '/Intranet/taxes/delete', [
           'App\Controllers\Entitys\TaxesController',
           'deleteAction'
       ]);
       
       return $routerContainer;
    }
}
