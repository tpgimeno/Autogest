<?php

require_once "../vendor/autoload.php";

/**
 * Use the function password_hash to encript the password
 * 
 */



password_hash('superSecurePassword', PASSWORD_DEFAULT);

ini_set('display_errors', 1);
ini_set('display_ startup_error', 1);
error_reporting(E_ALL);

use App\Middlewares\AuthenticationMiddleware;
use Aura\Router\RouterContainer;
use DI\ContainerBuilder;
use Dotenv\Dotenv;
use Franzl\Middleware\Whoops\WhoopsMiddleware;
use Illuminate\Database\Capsule\Manager as Capsule;
use Laminas\Diactoros\Response;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Middlewares\AuraRouter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use WoohooLabs\Harmony\Harmony;
use WoohooLabs\Harmony\Middleware\DispatcherMiddleware;
use WoohooLabs\Harmony\Middleware\LaminasEmitterMiddleware;


session_start();

$log = new Logger('app');
$log->pushHandler(new StreamHandler ( __DIR__ . '/../logs/app.log', Logger::WARNING));

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$capsule = new Capsule;

$capsule->addConnection([
    'driver'    => getenv('DB_DRIVER'),
    'host'      => getenv('DB_HOST'),
    'database'  => getenv('DB_NAME'),
    'username'  => getenv('DB_USER'),
    'password'  => getenv('DB_PASS'),
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
]);

// Make this Capsule instance available globally via static methods... (optional)
$capsule->setAsGlobal();

// Setup the Eloquent ORM... (optional; unless you've used setEventDispatcher())
$capsule->bootEloquent();

$request = ServerRequestFactory::fromGlobals(
    $_SERVER,
    $_GET,
    $_POST,
    $_COOKIE,
    $_FILES
    );   

$routerContainer = new RouterContainer();
$map = $routerContainer->getMap();

/*
 * GENERIC ROUTES
 */

$map->get('index', '/Intranet', [
    'App\Controllers\IndexController',
    'indexAction'    
]);
$map->post('login', '/intranet/login', [
    'App\Controllers\AuthController',
    'postLogin'
    
]);
$map->post('logout', '/intranet/logout', [
    'App\Controllers\AuthController',
    'getLogout'    
]);
$map->get('dashboard', '/intranet/admin', [
    'App\Controllers\AdminController',
    'getDashBoardAction'
]);

//COMPANY

$map->post('companyFormGet', '/intranet/company/form', [
    'App\Controllers\Entitys\CompanyController',
    'getCompanyDataAction'
]);
$map->get('companyFormPost', '/intranet/company/form', [
    'App\Controllers\Entitys\CompanyController',
    'getCompanyDataAction'
]);
$map->get('companyList', '/intranet/company/list', [
    'App\Controllers\Entitys\CompanyController',
    'getIndexAction'
]);
$map->post('searchCompany', '/intranet/company/search', [
    'App\Controllers\Entitys\CompanyController',
    'searchCompanyAction'
]);
$map->post('saveCompany', '/intranet/company/save', [
    'App\Controllers\Entitys\CompanyController',
    'getCompanyDataAction'
]);
$map->get('deleteCompany', '/intranet/company/delete', [        
    'App\Controllers\Entitys\CompanyController',
    'deleteAction'  
]);

//USERS

$map->get('userList', '/intranet/users/form', [
    'App\Controllers\Entitys\UsersController',
    'getAddUserAction'
]);
$map->get('userForm', '/intranet/users/list', [
    'App\Controllers\Entitys\UsersController',
    'getIndexUsers'
]);
$map->post('saveUser', '/intranet/users/form', [
    'App\Controllers\Entitys\UsersController',
    'getAddUserAction'
]);
$map->get('userDelete', '/intranet/users/delete', [
    'App\Controllers\Entitys\UsersController',
    'deleteAction'
]);

//SELLERS

$map->get('sellerList', '/intranet/sellers/form', [
    'App\Controllers\Entitys\SellersController',
    'getSellersDataAction'
]);
$map->get('sellerForm', '/intranet/sellers/list', [
    'App\Controllers\Entitys\SellersController',
    'getIndexAction'
]);
$map->post('saveSeller', '/intranet/sellers/save', [
    'App\Controllers\Entitys\SellersController',
    'getSellersDataAction'
]);
$map->get('sellerDelete', '/intranet/sellers/delete', [
    'App\Controllers\Entitys\SellersController',
    'deleteAction'
]);

//BANKS

$map->get('banksList', '/intranet/banks/form', [
    'App\Controllers\Entitys\BanksController',
    'getBankDataAction'
]);
$map->get('bankForm', '/intranet/banks/list', [
    'App\Controllers\Entitys\BanksController',
    'getIndexAction'    
]);
$map->post('saveBank', '/intranet/banks/save', [
    'App\Controllers\Entitys\BanksController',
    'getBankDataAction'    
]);
$map->get('bankDelete', '/intranet/banks/delete', [
    'App\Controllers\Entitys\BanksController',
    'deleteAction'    
]);

//FINANCE

$map->get('financeList', '/intranet/finance/form', [
    'App\Controllers\Sells\FinanceController',
    'getFinanceDataAction'
]);
$map->get('financeForm', '/intranet/finance/list', [
    'App\Controllers\Sells\FinanceController',
    'getIndexAction'    
]);
$map->post('saveFinance', '/intranet/finance/save', [
    'App\Controllers\Sells\FinanceController',
    'getFinanceDataAction'
]);
$map->get('financeDelete', '/intranet/finance/delete', [
    'App\Controllers\Sells\FinanceController',
    'deleteAction'    
]);

//STORES

$map->get('storesList', '/intranet/stores/form', [
    'App\Controllers\Entitys\StoreController',
    'getStoreDataAction'
]);
$map->get('storesForm', '/intranet/stores/list', [
    'App\Controllers\Entitys\StoreController',
    'getIndexAction'    
]);
$map->post('searchStore', '/intranet/stores/search', [
    'App\Controllers\Entitys\StoreController',
    'searchStore' 
]);
$map->post('saveStore', '/intranet/stores/save', [
    'App\Controllers\Entitys\StoreController',
    'getStoreDataAction' 
]);
$map->get('storesDelete', '/intranet/stores/delete', [
    'App\Controllers\Entitys\StoreController',
    'deleteAction'
]);

//LOCATIONS

$map->get('locationsList', '/intranet/locations/form', [
    'App\Controllers\Entitys\LocationController',
    'getLocationDataAction'
]);
$map->get('locationsForm', '/intranet/locations/list', [
    'App\Controllers\Entitys\LocationController',
    'getIndexAction'    
]);
$map->post('searchLocations', '/intranet/locations/search', [
    'App\Controllers\Entitys\LocationController',
    'searchLocationAction'
]);
$map->post('saveLocation', '/intranet/locations/save', [
    'App\Controllers\Entitys\LocationController',
    'getLocationDataAction' 
]);
$map->get('locationsDelete', '/intranet/locations/delete', [
    'App\Controllers\Entitys\LocationController',
    'deleteAction'
]);

//GARAGES 

$map->get('garagesList', '/intranet/buys/garages/form', [
    'App\Controllers\Sells\GaragesController',
    'getGarageDataAction'
]);
$map->get('garagesForm', '/intranet/buys/garages/list', [
    'App\Controllers\Sells\GaragesController',
    'getIndexAction'
]);
$map->post('searchGarage', '/intranet/buys/garages/search', [
    'App\Controllers\Sells\GaragesController',
    'searchGarageAction'
]);
$map->post('saveGarage', '/intranet/buys/garages/save', [
    'App\Controllers\Sells\GaragesController',
    'getGarageDataAction'
]);
$map->get('garagesDelete', '/intranet/buys/garages/delete', [
    'App\Controllers\Sells\GaragesController',
    'deleteAction'
]);

//PAYMENT-WAYS

$map->get('paymentWaysList', '/intranet/paymentWays/form', [
    'App\Controllers\PaymentWaysController',
    'getPaymentWaysDataAction'
]);
$map->get('paymentWaysForm', '/intranet/paymentWays/list', [
    'App\Controllers\PaymentWaysController',
    'getIndexAction'
]);
$map->post('savePaymentWays', '/intranet/paymentWays/save', [
    'App\Controllers\PaymentWaysController',
    'getPaymentWaysDataAction'
]);
$map->get('paymentWaysDelete', '/intranet/paymentWays/delete', [
    'App\Controllers\PaymentWaysController',
    'deleteAction'
]);

//RECIPES

$map->get('repicesList', '/intranet/sells/repices/form', [
    'App\Controllers\Sells\RecipesController',
    'getRecipesDataAction'
]);
$map->get('repicesForm', '/intranet/sells/repices/list', [
    'App\Controllers\Sells\RecipesController',
    'getIndexAction'
]);
$map->post('saveRecipes', '/intranet/sells/repices/save', [
    'App\Controllers\Sells\RecipesController',
    'getRecipesDataAction'
]);
$map->get('repicesDelete', '/intranet/sells/repices/delete', [
    'App\Controllers\Sells\RecipesController',
    'deleteAction'
]);
/*
 * SELLS ROUTES
 */

$map->get('customerForm', '/intranet/customers/form', [
    'App\Controllers\Sells\CustomerController',
    'getCustomerDataAction'
]);
$map->get('customerList', '/intranet/customers/list', [
    'App\Controllers\Sells\CustomerController',
    'getIndexAction'
]);
$map->post('searchCustomer', '/intranet/customers/search', [
    'App\Controllers\Sells\CustomerController',
    'searchCustomerAction'
]);
$map->post('saveCustomer', '/intranet/customers/save', [
    'App\Controllers\Sells\CustomerController',
    'getCustomerDataAction'
]);
$map->get('deleteCustomer', '/intranet/customers/delete', [        
    'App\Controllers\Sells\CustomerController',
    'deleteAction'   
]);
$map->get('customerTypeForm', '/intranet/customers/type/form', [
    'App\Controllers\Sells\CustomerTypesController',
    'getCustomerTypesDataAction'
]);
$map->get('customerTypeList', '/intranet/customers/type/list', [
    'App\Controllers\Sells\CustomerTypesController',
    'getIndexAction'
]);
$map->post('searchCustomerType', '/intranet/customers/type/search', [
    'App\Controllers\Sells\CustomerTypesController',
    'searchCustomerTypeAction'
]);
$map->post('saveCustomerType', '/intranet/customers/type/save', [
    'App\Controllers\Sells\CustomerTypesController',
    'getCustomerTypesDataAction'
]);
$map->get('deleteCustomerType', '/intranet/customers/type/delete', [        
    'App\Controllers\Sells\CustomerTypesController',
    'deleteAction'   
]);
$map->get('sellOffersForm', '/intranet/sells/offers/form', [
    'App\Controllers\Sells\SellOffersController',
    'getSellOffersDataAction'
]);
$map->get('sellOffersList', '/intranet/sells/offers/list', [
    'App\Controllers\Sells\SellOffersController',
    'getIndexAction'
]);
$map->post('searchSellOffers', '/intranet/sells/offers/search', [
    'App\Controllers\Sells\SellOffersController',
    'searchSellOffersAction'
]);
$map->post('searchCustomerSellOffers', '/intranet/sells/offers/customer/search', [
    'App\Controllers\Sells\SellOffersController',
    'searchCustomerSellOfferAction'
]);
$map->get('selectCustomerSellOffers', '/intranet/sells/offers/customer/select', [
    'App\Controllers\Sells\SellOffersController',
    'selectCustomerSellOfferAction'
]);
$map->post('searchVehicleSellOffers', '/intranet/sells/offers/vehicle/search', [
    'App\Controllers\Sells\SellOffersController',
    'searchVehicleSellOfferAction'
]);
$map->get('selectVehicleSellOffers', '/intranet/sells/offers/vehicle/select', [
    'App\Controllers\Sells\SellOffersController',
    'selectVehicleSellOfferAction'
]);
$map->post('searchComponentsSellOffers', '/intranet/sells/offers/components/search', [
    'App\Controllers\Sells\SellOffersController',
    'searchComponentsSellOffersAction'
]);
$map->get('selectComponentsSellOffers', '/intranet/sells/offers/components/select', [
    'App\Controllers\Sells\SellOffersController',
    'selectComponentsSellOffersAction'
]);
$map->post('addComponentsSellOffers', '/intranet/sells/offers/components/add', [
    'App\Controllers\Sells\SellOffersController',
    'addComponentsSellOffersAction'
]);
$map->get('editComponentsSellOffers', '/intranet/sells/offers/components/edit', [
    'App\Controllers\Sells\SellOffersController',
    'editComponentsSellOffersAction'
]);
$map->get('delComponentsSellOffers', '/intranet/sells/offers/components/del', [
    'App\Controllers\Sells\SellOffersController',
    'delComponentsSellOffersAction'
]);
$map->post('searchSuppliesSellOffers', '/intranet/sells/offers/supplies/search', [
    'App\Controllers\Sells\SellOffersController',
    'searchSuppliesSellOffersAction'
]);
$map->get('selectSuppliesSellOffers', '/intranet/sells/offers/supplies/select', [
    'App\Controllers\Sells\SellOffersController',
    'selectSuppliesSellOffersAction'
]);
$map->post('addSuppliesSellOffers', '/intranet/sells/offers/supplies/add', [
    'App\Controllers\Sells\SellOffersController',
    'addSuppliesSellOffersAction'
]);
$map->get('editSuppliesSellOffers', '/intranet/sells/offers/supplies/edit', [
    'App\Controllers\Sells\SellOffersController',
    'editSuppliesSellOffersAction'
]);
$map->get('delSuppliesSellOffers', '/intranet/sells/offers/supplies/del', [
    'App\Controllers\Sells\SellOffersController',
    'delSuppliesSellOffersAction'
]);
$map->post('searchWorksSellOffers', '/intranet/sells/offers/works/search', [
    'App\Controllers\Sells\SellOffersController',
    'searchWorksSellOffersAction'
]);
$map->get('selectWorksSellOffers', '/intranet/sells/offers/works/select', [
    'App\Controllers\Sells\SellOffersController',
    'selectWorksSellOffersAction'
]);
$map->post('addWorksSellOffers', '/intranet/sells/offers/works/add', [
    'App\Controllers\Sells\SellOffersController',
    'addWorksSellOffersAction'
]);
$map->get('editWorksSellOffers', '/intranet/sells/offers/works/edit', [
    'App\Controllers\Sells\SellOffersController',
    'editWorksSellOffersAction'
]);
$map->get('delWorksSellOffers', '/intranet/sells/offers/works/del', [
    'App\Controllers\Sells\SellOffersController',
    'delWorksSellOffersAction'
]);
$map->post('saveSellOffers', '/intranet/sells/offers/save', [
    'App\Controllers\Sells\SellOffersController',
    'getSellOffersDataAction'
]);
$map->get('deleteSellOffers', '/intranet/sells/offers/delete', [        
    'App\Controllers\Sells\SellOffersController',
    'deleteAction'   
]);
$map->get('sellDeliveriesForm', '/intranet/sells/sellDeliveries/form', [
    'App\Controllers\Sells\SellDeliveriesController',
    'getSellDeliveriesDataAction'
]);
$map->get('sellDeliveriesList', '/intranet/sells/sellDeliveries/list', [
    'App\Controllers\Sells\SellDeliveriesController',
    'getIndexAction'
]);
$map->post('searchSellDeliveries', '/intranet/sells/sellDeliveries/search', [
    'App\Controllers\Sells\SellDeliveriesController',
    'searchSellDeliveriesAction'
]);
$map->post('saveSellDeliveries', '/intranet/sells/sellDeliveries/save', [
    'App\Controllers\Sells\SellDeliveriesController',
    'getSellDeliveriesDataAction'
]);
$map->get('deleteSellDeliveries', '/intranet/sells/sellDeliveries/delete', [        
    'App\Controllers\Sells\SellDeliveriesController',
    'deleteAction'  
]);
$map->get('sellInvoicesForm', '/intranet/sells/invoices/form', [
    'App\Controllers\Sells\SellInvoicesController',
    'getSellInvoicesDataAction'
]);
$map->get('sellInvoicesList', '/intranet/sells/invoices/list', [
    'App\Controllers\Sells\SellInvoicesController',
    'getIndexAction'
]);
$map->post('searchSellInvoices', '/intranet/sells/invoices/search', [
    'App\Controllers\Sells\SellInvoicesController',
    'searchSellInvoicesAction'
]);
$map->post('saveSellInvoices', '/intranet/sells/invoices/save', [
    'App\Controllers\Sells\SellInvoicesController',
    'getSellInvoicesDataAction'
]);
$map->get('deleteSellInvoices', '/intranet/sells/invoices/delete', [        
    'App\Controllers\Sells\SellInvoicesController',
    'deleteAction'  
]);
/*
 * BUYS ROUTES
 */

//VEHICLE

$map->get('vehicleForm', '/intranet/vehicles/form', [
    'App\Controllers\Vehicle\VehicleController',
    'getVehicleDataAction'
]);
$map->get('vehicleList', '/intranet/vehicles/list', [
    'App\Controllers\Vehicle\VehicleController',
    'getIndexAction'
]);
$map->post('searchVehicle', '/intranet/vehicles/search', [
    'App\Controllers\Vehicle\VehicleController',
    'searchVehicleAction'
]);
$map->post('importVehicles', '/intranet/vehicles/import', [
    'App\Controllers\Vehicle\VehicleController',
    'importVehiclesExcel'
]);
$map->post('saveVehicle', '/intranet/vehicles/save', [
    'App\Controllers\Vehicle\VehicleController',
    'getVehicleDataAction'
]);
$map->get('deleteVehicle', '/intranet/vehicles/delete', [        
    'App\Controllers\Vehicle\VehicleController',
    'deleteAction'   
]);

//ACCESORIES

$map->get('accesoryForm', '/intranet/vehicles/accesories/form', [
    'App\Controllers\Vehicle\AccesoriesController',
    'getAccesoryDataAction'
]);
$map->get('accesoryList', '/intranet/vehicles/accesories/list', [
    'App\Controllers\Vehicle\AccesoriesController',
    'getIndexAction'
]);
$map->post('searchAccesory', '/intranet/vehicles/accesories/search', [
    'App\Controllers\Vehicle\AccesoriesController',
    'searchAccesoryAction'
]);
$map->post('saveAccesory', '/intranet/vehicles/accesories/save', [
    'App\Controllers\Vehicle\AccesoriesController',
    'getAccesoryDataAction'
]);
$map->get('deleteAccesory', '/intranet/vehicles/accesories/delete', [        
    'App\Controllers\Vehicle\AccesoriesController',
    'deleteAction'   
]);
$map->post('addVehicleAccesory', '/intranet/vehicles/accesories/add', [        
    'App\Controllers\Vehicle\VehicleController',
    'addAccesoryAction'   
]);
$map->post('deleteVehicleAccesory', '/intranet/vehicles/accesories/del', [        
    'App\Controllers\Vehicle\VehicleController',
    'deleteAccesoryAction'   
]);

//BRANDS

$map->get('brandForm', '/intranet/vehicles/brands/form', [
    'App\Controllers\Vehicle\BrandController',
    'getBrandDataAction'
]);
$map->get('brandList', '/intranet/vehicles/brands/list', [
    'App\Controllers\Vehicle\BrandController',
    'getIndexAction'
]);
$map->post('searchBrand', '/intranet/vehicles/brands/search', [
    'App\Controllers\Vehicle\BrandController',
    'searchBrandAction'
]);
$map->post('saveBrand', '/intranet/vehicles/brands/save', [
    'App\Controllers\Vehicle\BrandController',
    'getBrandDataAction'
]);
$map->get('deleteBrand', '/intranet/vehicles/brands/delete', [        
    'App\Controllers\Vehicle\BrandController',
    'deleteAction'   
]);

//MODELS

$map->get('modelForm', '/intranet/vehicles/models/form', [
    'App\Controllers\Vehicle\ModelController',
    'getModelDataAction'
]);
$map->get('modelList', '/intranet/vehicles/models/list', [
    'App\Controllers\Vehicle\ModelController',
    'getIndexAction'
]);
$map->post('searchModel', '/intranet/vehicles/models/search', [
    'App\Controllers\Vehicle\ModelController',
    'searchModelAction'
]);
$map->post('saveModel', '/intranet/vehicles/models/save', [
    'App\Controllers\Vehicle\ModelController',
    'getModelDataAction'
]);
$map->get('deleteModel', '/intranet/vehicles/models/delete', [        
    'App\Controllers\Vehicle\ModelController',
    'deleteAction'   
]);

//WORKS

$map->get('WorksForm', '/intranet/vehicles/works/form', [
    'App\Controllers\Vehicle\WorksController',
    'getWorkDataAction'
]);
$map->get('WorksList', '/intranet/vehicles/works/list', [
    'App\Controllers\Vehicle\WorksController',
    'getIndexAction'
]);
$map->post('searchWorks', '/intranet/vehicles/works/search', [
    'App\Controllers\Vehicle\WorksController',
    'searchWorksAction'
]);
$map->post('saveWorks', '/intranet/vehicles/works/save', [
    'App\Controllers\Vehicle\WorksController',
    'getWorkDataAction'
]);
$map->get('deleteWorks', '/intranet/vehicles/works/delete', [        
    'App\Controllers\Vehicle\WorksController',
    'deleteAction'   
]);

//VEHICLES TYPES

$map->get('vehicleTypesForm', '/intranet/vehicles/vehicleTypes/form', [
    'App\Controllers\Vehicle\VehicleTypesController',
    'getVehicleTypesDataAction'
]);
$map->get('vehicleTypesList', '/intranet/vehicles/vehicleTypes/list', [
    'App\Controllers\Vehicle\VehicleTypesController',
    'getIndexAction'
]);
$map->post('searchVehicleTypes', '/intranet/vehicles/vehicleTypes/search', [
    'App\Controllers\Vehicle\VehicleTypesController',
    'searchVehicleTypesAction'
]);
$map->post('saveVehicleTypes', '/intranet/vehicles/vehicleTypes/save', [
    'App\Controllers\Vehicle\VehicleTypesController',
    'getVehicleTypesDataAction'
]);
$map->get('deleteVehicleTypes', '/intranet/vehicles/vehicleTypes/delete', [        
    'App\Controllers\Vehicle\VehicleTypesController',
    'deleteAction'   
]);

//BUY DELIVERIES

$map->get('buyDeliveriesForm', '/intranet/buys/buyDeliveries/form', [
    'App\Controllers\Buys\BuyDeliveriesController',
    'getBuyDeliveriesDataAction'
]);
$map->get('buyDeliveriesList', '/intranet/buys/buyDeliveries/list', [
    'App\Controllers\Buys\BuyDeliveriesController',
    'getIndexAction'
]);
$map->post('searchBuyDeliveries', '/intranet/buys/buyDeliveries/search', [
    'App\Controllers\Buys\BuyDeliveriesController',
    'searchBuyDeliveriesAction'
]);
$map->post('saveBuyDeliveries', '/intranet/buys/buyDeliveries/save', [
    'App\Controllers\Buys\BuyDeliveriesController',
    'getBuyDeliveriesDataAction'
]);
$map->get('deleteBuyDeliveries', '/intranet/buys/buyDeliveries/delete', [        
    'App\Controllers\Buys\BuyDeliveriesController',
    'deleteAction'  
]);

//BUY INVOICES

$map->get('buyInvoicesForm', '/intranet/buys/buyInvoices/form', [
    'App\Controllers\Buys\BuyInvoicesController',
    'getBuyInvoicesDataAction'
]);
$map->get('buyInvoicesList', '/intranet/buys/buyInvoices/list', [
    'App\Controllers\Buys\BuyInvoicesController',
    'getIndexAction'
]);
$map->post('searchBuyInvoices', '/intranet/buys/buyInvoices/search', [
    'App\Controllers\Buys\BuyInvoicesController',
    'searchBuyInvoicesAction'
]);
$map->post('saveBuyInvoices', '/intranet/buys/buyInvoices/save', [
    'App\Controllers\Buys\BuyInvoicesController',
    'getBuyInvoicesDataAction'
]);
$map->get('deleteBuyInvoices', '/intranet/buys/buyInvoices/delete', [        
    'App\Controllers\Buys\BuyInvoicesController',
    'deleteAction'  
]);

//VEHICLE COMPONENTS

$map->get('componentsForm', '/intranet/buys/components/form', [
    'App\Controllers\Vehicle\ComponentsController',
    'getComponentsDataAction'
]);
$map->get('componentsList', '/intranet/buys/components/list', [
    'App\Controllers\Vehicle\ComponentsController',
    'getIndexAction'
]);
$map->post('searchComponents', '/intranet/buys/components/search', [
    'App\Controllers\Vehicle\ComponentsController',
    'searchComponentsAction'
]);
$map->post('saveComponents', '/intranet/buys/components/save', [
    'App\Controllers\Vehicle\ComponentsController',
    'getComponentsDataAction'
]);
$map->get('deleteComponents', '/intranet/buys/components/delete', [        
    'App\Controllers\Vehicle\ComponentsController',
    'deleteAction'  
]);

//VEHICLE SUPPLIES

$map->get('suppliesForm', '/intranet/buys/supplies/form', [
    'App\Controllers\Vehicle\SuppliesController',
    'getSuppliesDataAction'
]);
$map->get('suppliesList', '/intranet/buys/supplies/list', [
    'App\Controllers\Vehicle\SuppliesController',
    'getIndexAction'
]);
$map->post('searchSupplies', '/intranet/buys/supplies/search', [
    'App\Controllers\Vehicle\SuppliesController',
    'searchSuppliesAction'
]);
$map->post('saveSupplies', '/intranet/buys/supplies/save', [
    'App\Controllers\Vehicle\SuppliesController',
    'getSuppliesDataAction'
]);
$map->get('deleteSupplies', '/intranet/buys/supplies/delete', [        
    'App\Controllers\Vehicle\SuppliesController',
    'deleteAction'  
]);

//PROVIDERS

$map->get('providersList', '/intranet/buys/providers/form', [
    'App\Controllers\Buys\ProvidersController',
    'getProviderDataAction'
]);
$map->get('providersForm', '/intranet/buys/providers/list', [
    'App\Controllers\Buys\ProvidersController',
    'getIndexAction'
]);
$map->post('searchProvider', '/intranet/buys/providers/search', [
    'App\Controllers\Buys\ProvidersController',
    'searchProviderAction'
]);
$map->post('saveProvider', '/intranet/buys/providers/save', [
    'App\Controllers\Buys\ProvidersController',
    'getProviderDataAction'
]);
$map->get('providersDelete', '/intranet/buys/providers/delete', [
    'App\Controllers\Buys\ProvidersController',
    'deleteAction'
]);

//MADERS

$map->get('madersList', '/intranet/buys/maders/form', [
    'App\Controllers\Buys\MadersController',
    'getMaderDataAction'
]);
$map->get('madersForm', '/intranet/buys/maders/list', [
    'App\Controllers\Buys\MadersController',
    'getIndexAction'    
]);
$map->post('saveMader', '/intranet/buys/maders/save', [
    'App\Controllers\Buys\MadersController',
    'getMaderDataAction' 
]);
$map->get('madersDelete', '/intranet/buys/maders/delete', [
    'App\Controllers\Buys\MadersController',
    'deleteAction'
]);

//GARAGE ORDERS

$map->get('ordersList', '/intranet/buys/orders/form', [
    'App\Controllers\Garages\GarageOrdersController',
    'getOrderDataAction'
]);
$map->get('ordersForm', '/intranet/buys/orders/list', [
    'App\Controllers\Garages\GarageOrdersController',
    'getIndexAction'
]);
$map->post('saveWork', '/intranet/buys/orders/form/work/save', [
    'App\Controllers\Garages\GarageOrdersController',
    'getOrderDataAction'
]);
$map->post('saveOrder', '/intranet/buys/orders/save', [
    'App\Controllers\Garages\GarageOrdersController',
    'getOrderDataAction'
]);
$map->get('ordersDelete', '/intranet/buys/orders/delete', [
    'App\Controllers\Garages\GarageOrdersController',
    'deleteAction'
]);
/*
 * PRODUCTION ROUTES
 */
$map->get('productionList', '/intranet/production/form', [
    'App\Controllers\Entitys\ProductionController',
    'getProductionDataAction'
]);
$map->get('productionForm', '/intranet/production/list', [
    'App\Controllers\Entitys\ProductionController',
    'getIndexAction'
]);
$map->post('saveProduction', '/intranet/production/save', [
    'App\Controllers\Entitys\ProductionController',
    'getProductionDataAction'
]);
$map->get('productionDelete', '/intranet/production/delete', [
    'App\Controllers\Entitys\ProductionController',
    'deleteAction'
]);

// REPORTS


$map->get('pruebaReport', '/intranet/reports/prueba', [
    'App\Controllers\PruebaReportController',
    'getReportAction'
]);
$map->post('SellOfferVehicleReport', '/intranet/reports/sellofferVehicle', [
    'App\Controllers\Sells\SellOffersController',
    'getVehicleReportAction'
]);
$map->post('SellOfferDetailedReport', '/intranet/reports/sellofferDetailed', [
    'App\Controllers\Sells\SellOffersController',
    'getDetailedReportAction'
]);
$map->post('SellOfferIntraReport', '/intranet/reports/sellofferIntra', [
    'App\Controllers\Sells\SellOffersController',
    'getIntraReportAction'
]);
$map->post('SellOfferExportReport', '/intranet/reports/sellofferExport', [
    'App\Controllers\Sells\SellOffersController',
    'getExportReportAction'
]);



// FIN DE LAS RUTAS




$builder = new ContainerBuilder();
$container = $builder->build();

$matcher = $routerContainer->getMatcher();
$route = $matcher->match($request);
if(!$route)
{
    echo 'No route' . '</br>';
}else
{ 
   try
    {
        $harmony = new Harmony($request, new Response());
            $harmony
                ->addMiddleware(new LaminasEmitterMiddleware(new SapiEmitter()));
        if(getenv('DEBUG') === "true")
        {
            $harmony
                    ->addMiddleware(new WhoopsMiddleware());
        }
        $harmony    
            ->addMiddleware(new AuthenticationMiddleware())
            ->addMiddleware(new AuraRouter($routerContainer))
            ->addMiddleware(new DispatcherMiddleware( $container, 'request-handler'))
            ->run();
    }
    catch (Exception $ex) 
    {
        $log->warning($ex->getMessage());
        $emitter = new SapiEmitter();
        $emitter->emit(new Response\EmptyResponse(400));
    }
    catch (Error $err)
    {
        $log->error($err->getMessage());
        $emitter = new SapiEmitter();
        $emitter->emit(new Response\EmptyResponse(500));
    }
}
