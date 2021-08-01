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

//BANKS

$map->get('banksList', '/Intranet/banks/form', [
    'App\Controllers\Entitys\BanksController',
    'getBankDataAction'
]);
$map->get('bankForm', '/Intranet/banks/list', [
    'App\Controllers\Entitys\BanksController',
    'getIndexAction'    
]);
$map->post('saveBank', '/Intranet/banks/save', [
    'App\Controllers\Entitys\BanksController',
    'getBankDataAction'    
]);
$map->get('bankDelete', '/Intranet/banks/delete', [
    'App\Controllers\Entitys\BanksController',
    'deleteAction'    
]);

//FINANCE

$map->get('financeList', '/Intranet/finance/form', [
    'App\Controllers\Sells\FinanceController',
    'getFinanceDataAction'
]);
$map->get('financeForm', '/Intranet/finance/list', [
    'App\Controllers\Sells\FinanceController',
    'getIndexAction'    
]);
$map->post('saveFinance', '/Intranet/finance/save', [
    'App\Controllers\Sells\FinanceController',
    'getFinanceDataAction'
]);
$map->get('financeDelete', '/Intranet/finance/delete', [
    'App\Controllers\Sells\FinanceController',
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
    'searchStore' 
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

$map->get('garagesList', '/Intranet/buys/garages/form', [
    'App\Controllers\Sells\GaragesController',
    'getGarageDataAction'
]);
$map->get('garagesForm', '/Intranet/buys/garages/list', [
    'App\Controllers\Sells\GaragesController',
    'getIndexAction'
]);
$map->post('searchGarage', '/Intranet/buys/garages/search', [
    'App\Controllers\Sells\GaragesController',
    'searchGarageAction'
]);
$map->post('saveGarage', '/Intranet/buys/garages/save', [
    'App\Controllers\Sells\GaragesController',
    'getGarageDataAction'
]);
$map->get('garagesDelete', '/Intranet/buys/garages/delete', [
    'App\Controllers\Sells\GaragesController',
    'deleteAction'
]);

//PAYMENT-WAYS

$map->get('paymentWaysList', '/Intranet/paymentWays/form', [
    'App\Controllers\PaymentWaysController',
    'getPaymentWaysDataAction'
]);
$map->get('paymentWaysForm', '/Intranet/paymentWays/list', [
    'App\Controllers\PaymentWaysController',
    'getIndexAction'
]);
$map->post('savePaymentWays', '/Intranet/paymentWays/save', [
    'App\Controllers\PaymentWaysController',
    'getPaymentWaysDataAction'
]);
$map->get('paymentWaysDelete', '/Intranet/paymentWays/delete', [
    'App\Controllers\PaymentWaysController',
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
/*
 * SELLS ROUTES
 */

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

$map->get('componentsForm', '/Intranet/buys/components/form', [
    'App\Controllers\Vehicle\ComponentsController',
    'getComponentsDataAction'
]);
$map->get('componentsList', '/Intranet/buys/components/list', [
    'App\Controllers\Vehicle\ComponentsController',
    'getIndexAction'
]);
$map->post('searchComponents', '/Intranet/buys/components/search', [
    'App\Controllers\Vehicle\ComponentsController',
    'searchComponentsAction'
]);
$map->post('saveComponents', '/Intranet/buys/components/save', [
    'App\Controllers\Vehicle\ComponentsController',
    'getComponentsDataAction'
]);
$map->get('deleteComponents', '/Intranet/buys/components/delete', [        
    'App\Controllers\Vehicle\ComponentsController',
    'deleteAction'  
]);

//VEHICLE SUPPLIES

$map->get('suppliesForm', '/Intranet/buys/supplies/form', [
    'App\Controllers\Vehicle\SuppliesController',
    'getSuppliesDataAction'
]);
$map->get('suppliesList', '/Intranet/buys/supplies/list', [
    'App\Controllers\Vehicle\SuppliesController',
    'getIndexAction'
]);
$map->post('searchSupplies', '/Intranet/buys/supplies/search', [
    'App\Controllers\Vehicle\SuppliesController',
    'searchSuppliesAction'
]);
$map->post('saveSupplies', '/Intranet/buys/supplies/save', [
    'App\Controllers\Vehicle\SuppliesController',
    'getSuppliesDataAction'
]);
$map->get('deleteSupplies', '/Intranet/buys/supplies/delete', [        
    'App\Controllers\Vehicle\SuppliesController',
    'deleteAction'  
]);

//PROVIDERS

$map->get('providersList', '/Intranet/buys/providers/form', [
    'App\Controllers\Buys\ProvidersController',
    'getProviderDataAction'
]);
$map->get('providersForm', '/Intranet/buys/providers/list', [
    'App\Controllers\Buys\ProvidersController',
    'getIndexAction'
]);
$map->post('searchProvider', '/Intranet/buys/providers/search', [
    'App\Controllers\Buys\ProvidersController',
    'searchProviderAction'
]);
$map->post('saveProvider', '/Intranet/buys/providers/save', [
    'App\Controllers\Buys\ProvidersController',
    'getProviderDataAction'
]);
$map->get('providersDelete', '/Intranet/buys/providers/delete', [
    'App\Controllers\Buys\ProvidersController',
    'deleteAction'
]);

//MADERS

$map->get('madersList', '/Intranet/buys/maders/form', [
    'App\Controllers\Buys\MadersController',
    'getMaderDataAction'
]);
$map->get('madersForm', '/Intranet/buys/maders/list', [
    'App\Controllers\Buys\MadersController',
    'getIndexAction'    
]);
$map->post('saveMader', '/Intranet/buys/maders/save', [
    'App\Controllers\Buys\MadersController',
    'getMaderDataAction' 
]);
$map->get('madersDelete', '/Intranet/buys/maders/delete', [
    'App\Controllers\Buys\MadersController',
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
/*
 * PRODUCTION ROUTES
 */
$map->get('productionList', '/Intranet/production/form', [
    'App\Controllers\Entitys\ProductionController',
    'getProductionDataAction'
]);
$map->get('productionForm', '/Intranet/production/list', [
    'App\Controllers\Entitys\ProductionController',
    'getIndexAction'
]);
$map->post('saveProduction', '/Intranet/production/save', [
    'App\Controllers\Entitys\ProductionController',
    'getProductionDataAction'
]);
$map->get('productionDelete', '/Intranet/production/delete', [
    'App\Controllers\Entitys\ProductionController',
    'deleteAction'
]);

// REPORTS


$map->get('pruebaReport', '/Intranet/reports/prueba', [
    'App\Controllers\PruebaReportController',
    'getReportAction'
]);
$map->post('SellOfferVehicleReport', '/Intranet/reports/sellofferVehicle', [
    'App\Controllers\Sells\SellOffersController',
    'getVehicleReportAction'
]);
$map->post('SellOfferDetailedReport', '/Intranet/reports/sellofferDetailed', [
    'App\Controllers\Sells\SellOffersController',
    'getDetailedReportAction'
]);
$map->post('SellOfferIntraReport', '/Intranet/reports/sellofferIntra', [
    'App\Controllers\Sells\SellOffersController',
    'getIntraReportAction'
]);
$map->post('SellOfferExportReport', '/Intranet/reports/sellofferExport', [
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
