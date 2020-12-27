<?php

require_once "../vendor/autoload.php";


//Aquí tenemos la encriptación del password obtenido.

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

$map->get('index', '/', [
    'App\Controllers\IndexController',
    'indexAction'    
]);
$map->post('login', '/login', [
    'App\Controllers\AuthController',
    'postLogin'
    
]);
$map->post('logout', '/logout', [
    'App\Controllers\AuthController',
    'getLogout'    
]);
$map->get('dashboard', '/admin', [
    'App\Controllers\AdminController',
    'getDashBoardAction'
]);
<<<<<<< HEAD

$map->get('companyForm', '/company/form', [
=======
$map->post('companyFormGet', '/intranet/company/form', [
    'App\Controllers\Entitys\CompanyController',
    'getCompanyDataAction'
]);
$map->get('companyFormPost', '/intranet/company/form', [
>>>>>>> RepairMigrations
    'App\Controllers\Entitys\CompanyController',
    'getCompanyDataAction'
]);
$map->get('companyList', '/company/list', [
    'App\Controllers\Entitys\CompanyController',
    'getIndexAction'
]);
$map->post('searchCompany', '/company/search', [
    'App\Controllers\Entitys\CompanyController',
    'searchCompanyAction'
]);
$map->post('saveCompany', '/company/save', [
    'App\Controllers\Entitys\CompanyController',
    'getCompanyDataAction'
]);
$map->get('deleteCompany', '/company/delete', [        
    'App\Controllers\Entitys\CompanyController',
    'deleteAction'  
]);
$map->get('userList', '/users/form', [
    'App\Controllers\UsersController',
    'getAddUserAction'
]);
$map->get('userForm', '/users/list', [
    'App\Controllers\UsersController',
    'getIndexUsers'
]);
$map->post('saveUser', '/users/form', [
    'App\Controllers\UsersController',
    'getAddUserAction'
]);
$map->get('userDelete', '/users/delete', [
    'App\Controllers\UsersController',
    'deleteAction'
]);
$map->get('sellerList', '/sellers/form', [
    'App\Controllers\Entitys\SellersController',
    'getSellersDataAction'
]);
$map->get('sellerForm', '/sellers/list', [
    'App\Controllers\Entitys\SellersController',
    'getIndexAction'
]);
$map->post('saveSeller', '/sellers/save', [
    'App\Controllers\Entitys\SellersController',
    'getSellersDataAction'
]);
$map->get('sellerDelete', '/sellers/delete', [
    'App\Controllers\Entitys\SellersController',
    'deleteAction'
]);
$map->get('banksList', '/banks/form', [
    'App\Controllers\Entitys\BanksController',
    'getBankDataAction'
]);
$map->get('bankForm', '/banks/list', [
    'App\Controllers\Entitys\BanksController',
    'getIndexAction'
    
]);
$map->post('saveBank', '/banks/save', [
    'App\Controllers\Entitys\BanksController',
    'getBankDataAction'
    
]);
$map->get('bankDelete', '/banks/delete', [
    'App\Controllers\Entitys\BanksController',
    'deleteAction'
    
]);
$map->get('financeList', '/finance/form', [
    'App\Controllers\Entitys\FinanceController',
    'getFinanceDataAction'
]);
$map->get('financeForm', '/finance/list', [
    'App\Controllers\Entitys\FinanceController',
    'getIndexAction'
    
]);
$map->post('saveFinance', '/finance/save', [
    'App\Controllers\Entitys\FinanceController',
    'getFinanceDataAction'
]);
$map->get('financeDelete', '/finance/delete', [
    'App\Controllers\Entitys\FinanceController',
    'deleteAction'
    
]);
$map->get('storesList', '/stores/form', [
    'App\Controllers\Entitys\StoreController',
    'getStoreDataAction'
]);
$map->get('storesForm', '/stores/list', [
    'App\Controllers\Entitys\StoreController',
    'getIndexAction'    
]);
$map->post('searchStore', '/stores/search', [
    'App\Controllers\Entitys\StoreController',
    'searchStore' 
]);
$map->post('saveStore', '/stores/save', [
    'App\Controllers\Entitys\StoreController',
    'getStoreDataAction' 
]);
$map->get('storesDelete', '/stores/delete', [
    'App\Controllers\Entitys\StoreController',
    'deleteAction'
]);
$map->get('locationsList', '/locations/form', [
    'App\Controllers\Entitys\LocationController',
    'getLocationDataAction'
]);
$map->get('locationsForm', '/locations/list', [
    'App\Controllers\Entitys\LocationController',
    'getIndexAction'    
]);
$map->post('searchLocations', '/locations/search', [
    'App\Controllers\Entitys\LocationController',
    'searchLocationAction'
]);
$map->post('saveLocation', '/locations/save', [
    'App\Controllers\Entitys\LocationController',
    'getLocationDataAction' 
]);
$map->get('locationsDelete', '/locations/delete', [
    'App\Controllers\Entitys\LocationController',
    'deleteAction'
]);
$map->get('garagesList', '/buys/garages/form', [
    'App\Controllers\Buys\GaragesController',
    'getGarageDataAction'
]);
$map->get('garagesForm', '/buys/garages/list', [
    'App\Controllers\Buys\GaragesController',
    'getIndexAction'
]);
$map->post('searchGarage', '/buys/garages/search', [
    'App\Controllers\Buys\GaragesController',
    'searchGarageAction'
]);
$map->post('saveGarage', '/buys/garages/save', [
    'App\Controllers\Buys\GaragesController',
    'getGarageDataAction'
]);
$map->get('garagesDelete', '/buys/garages/delete', [
    'App\Controllers\Buys\GaragesController',
    'deleteAction'
]);
$map->get('paymentWaysList', '/paymentWays/form', [
    'App\Controllers\PaymentWaysController',
    'getPaymentWaysDataAction'
]);
$map->get('paymentWaysForm', '/paymentWays/list', [
    'App\Controllers\PaymentWaysController',
    'getIndexAction'
]);
$map->post('savePaymentWays', '/paymentWays/save', [
    'App\Controllers\PaymentWaysController',
    'getPaymentWaysDataAction'
]);
$map->get('paymentWaysDelete', '/paymentWays/delete', [
    'App\Controllers\PaymentWaysController',
    'deleteAction'
]);
$map->get('repicesList', '/sells/repices/form', [
    'App\Controllers\Sells\RecipesController',
    'getRecipesDataAction'
]);
$map->get('repicesForm', '/sells/repices/list', [
    'App\Controllers\Sells\RecipesController',
    'getIndexAction'
]);
$map->post('saveRecipes', '/sells/repices/save', [
    'App\Controllers\Sells\RecipesController',
    'getRecipesDataAction'
]);
$map->get('repicesDelete', '/sells/repices/delete', [
    'App\Controllers\Sells\RecipesController',
    'deleteAction'
]);
/*
 * SELLS ROUTES
 */

<<<<<<< HEAD
$map->get('customerForm', '/customers/form', [
    'App\Controllers\Entitys\CustomerController',
    'getCustomerDataAction'
]);
$map->get('customerList', '/customers/list', [
    'App\Controllers\Entitys\CustomerController',
    'getIndexAction'
]);
$map->post('searchCustomer', '/customers/search', [
    'App\Controllers\Entitys\CustomerController',
    'searchCustomerAction'
]);
$map->post('saveCustomer', '/customers/save', [
    'App\Controllers\Entitys\CustomerController',
    'getCustomerDataAction'
]);
$map->get('deleteCustomer', '/customers/delete', [        
    'App\Controllers\Entitys\CustomerController',
    'deleteAction'   
]);
$map->get('customerTypeForm', '/customers/type/form', [
    'App\Controllers\Crm\CustomerTypesController',
    'getCustomerTypesDataAction'
]);
$map->get('customerTypeList', '/customers/type/list', [
    'App\Controllers\Crm\CustomerTypesController',
    'getIndexAction'
]);
$map->post('searchCustomerType', '/customers/type/search', [
    'App\Controllers\Crm\CustomerTypesController',
    'searchCustomerTypeAction'
]);
$map->post('saveCustomerType', '/customers/type/save', [
    'App\Controllers\Crm\CustomerTypesController',
    'getCustomerTypesDataAction'
]);
$map->get('deleteCustomerType', '/customers/type/delete', [        
    'App\Controllers\Crm\CustomerTypesController',
    'deleteAction'   
]);
$map->get('sellOffersForm', '/crm/offers/form', [
    'App\Controllers\Crm\SellOffersController',
    'getSellOffersDataAction'
]);
$map->get('sellOffersList', '/crm/offers/list', [
    'App\Controllers\Crm\SellOffersController',
    'getIndexAction'
]);
$map->post('searchSellOffers', '/crm/offers/search', [
    'App\Controllers\Crm\SellOffersController',
    'searchSellOffersAction'
]);
$map->post('searchCustomerSellOffers', '/crm/offers/customer/search', [
    'App\Controllers\Crm\SellOffersController',
    'searchCustomerSellOfferAction'
]);
$map->get('selectCustomerSellOffers', '/crm/offers/customer/select', [
    'App\Controllers\Crm\SellOffersController',
    'selectCustomerSellOfferAction'
]);
$map->post('searchVehicleSellOffers', '/crm/offers/vehicle/search', [
    'App\Controllers\Crm\SellOffersController',
    'searchVehicleSellOfferAction'
]);
$map->get('selectVehicleSellOffers', '/crm/offers/vehicle/select', [
    'App\Controllers\Crm\SellOffersController',
    'selectVehicleSellOfferAction'
]);
$map->post('searchComponentsSellOffers', '/crm/offers/components/search', [
    'App\Controllers\Crm\SellOffersController',
    'searchComponentsSellOffersAction'
]);
$map->get('selectComponentsSellOffers', '/crm/offers/components/select', [
    'App\Controllers\Crm\SellOffersController',
    'selectComponentsSellOffersAction'
]);
$map->post('addComponentsSellOffers', '/crm/offers/components/add', [
    'App\Controllers\Crm\SellOffersController',
    'addComponentsSellOffersAction'
]);
$map->get('editComponentsSellOffers', '/crm/offers/components/edit', [
    'App\Controllers\Crm\SellOffersController',
    'editComponentsSellOffersAction'
]);
$map->get('delComponentsSellOffers', '/crm/offers/components/del', [
    'App\Controllers\Crm\SellOffersController',
    'delComponentsSellOffersAction'
]);
$map->post('searchSuppliesSellOffers', '/crm/offers/supplies/search', [
    'App\Controllers\Crm\SellOffersController',
    'searchSuppliesSellOffersAction'
]);
$map->get('selectSuppliesSellOffers', '/crm/offers/supplies/select', [
    'App\Controllers\Crm\SellOffersController',
    'selectSuppliesSellOffersAction'
]);
$map->post('addSuppliesSellOffers', '/crm/offers/supplies/add', [
    'App\Controllers\Crm\SellOffersController',
    'addSuppliesSellOffersAction'
]);
$map->get('editSuppliesSellOffers', '/crm/offers/supplies/edit', [
    'App\Controllers\Crm\SellOffersController',
    'editSuppliesSellOffersAction'
]);
$map->get('delSuppliesSellOffers', '/crm/offers/supplies/del', [
    'App\Controllers\Crm\SellOffersController',
    'delSuppliesSellOffersAction'
]);
$map->post('searchWorksSellOffers', '/crm/offers/works/search', [
    'App\Controllers\Crm\SellOffersController',
    'searchWorksSellOffersAction'
]);
$map->get('selectWorksSellOffers', '/crm/offers/works/select', [
    'App\Controllers\Crm\SellOffersController',
    'selectWorksSellOffersAction'
]);
$map->post('addWorksSellOffers', '/crm/offers/works/add', [
    'App\Controllers\Crm\SellOffersController',
    'addWorksSellOffersAction'
]);
$map->get('editWorksSellOffers', '/crm/offers/works/edit', [
    'App\Controllers\Crm\SellOffersController',
    'editWorksSellOffersAction'
]);
$map->get('delWorksSellOffers', '/crm/offers/works/del', [
    'App\Controllers\Crm\SellOffersController',
    'delWorksSellOffersAction'
]);
$map->post('saveSellOffers', '/crm/offers/save', [
    'App\Controllers\Crm\SellOffersController',
    'getSellOffersDataAction'
]);
$map->get('deleteSellOffers', '/crm/offers/delete', [        
    'App\Controllers\Crm\SellOffersController',
=======
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
$map->get('sellOffersForm', '/intranet/crm/offers/form', [
    'App\Controllers\Sells\SellOffersController',
    'getSellOffersDataAction'
]);
$map->get('sellOffersList', '/intranet/crm/offers/list', [
    'App\Controllers\Sells\SellOffersController',
    'getIndexAction'
]);
$map->post('searchSellOffers', '/intranet/crm/offers/search', [
    'App\Controllers\Sells\SellOffersController',
    'searchSellOffersAction'
]);
$map->post('searchCustomerSellOffers', '/intranet/crm/offers/customer/search', [
    'App\Controllers\Sells\SellOffersController',
    'searchCustomerSellOfferAction'
]);
$map->get('selectCustomerSellOffers', '/intranet/crm/offers/customer/select', [
    'App\Controllers\Sells\SellOffersController',
    'selectCustomerSellOfferAction'
]);
$map->post('searchVehicleSellOffers', '/intranet/crm/offers/vehicle/search', [
    'App\Controllers\Sells\SellOffersController',
    'searchVehicleSellOfferAction'
]);
$map->get('selectVehicleSellOffers', '/intranet/crm/offers/vehicle/select', [
    'App\Controllers\Sells\SellOffersController',
    'selectVehicleSellOfferAction'
]);
$map->post('searchComponentsSellOffers', '/intranet/crm/offers/components/search', [
    'App\Controllers\Sells\SellOffersController',
    'searchComponentsSellOffersAction'
]);
$map->get('selectComponentsSellOffers', '/intranet/crm/offers/components/select', [
    'App\Controllers\Sells\SellOffersController',
    'selectComponentsSellOffersAction'
]);
$map->post('addComponentsSellOffers', '/intranet/crm/offers/components/add', [
    'App\Controllers\Sells\SellOffersController',
    'addComponentsSellOffersAction'
]);
$map->get('editComponentsSellOffers', '/intranet/crm/offers/components/edit', [
    'App\Controllers\Sells\SellOffersController',
    'editComponentsSellOffersAction'
]);
$map->get('delComponentsSellOffers', '/intranet/crm/offers/components/del', [
    'App\Controllers\Sells\SellOffersController',
    'delComponentsSellOffersAction'
]);
$map->post('searchSuppliesSellOffers', '/intranet/crm/offers/supplies/search', [
    'App\Controllers\Sells\SellOffersController',
    'searchSuppliesSellOffersAction'
]);
$map->get('selectSuppliesSellOffers', '/intranet/crm/offers/supplies/select', [
    'App\Controllers\Sells\SellOffersController',
    'selectSuppliesSellOffersAction'
]);
$map->post('addSuppliesSellOffers', '/intranet/crm/offers/supplies/add', [
    'App\Controllers\Sells\SellOffersController',
    'addSuppliesSellOffersAction'
]);
$map->get('editSuppliesSellOffers', '/intranet/crm/offers/supplies/edit', [
    'App\Controllers\Sells\SellOffersController',
    'editSuppliesSellOffersAction'
]);
$map->get('delSuppliesSellOffers', '/intranet/crm/offers/supplies/del', [
    'App\Controllers\Sells\SellOffersController',
    'delSuppliesSellOffersAction'
]);
$map->post('searchWorksSellOffers', '/intranet/crm/offers/works/search', [
    'App\Controllers\Sellsm\SellOffersController',
    'searchWorksSellOffersAction'
]);
$map->get('selectWorksSellOffers', '/intranet/crm/offers/works/select', [
    'App\Controllers\Sells\SellOffersController',
    'selectWorksSellOffersAction'
]);
$map->post('addWorksSellOffers', '/intranet/crm/offers/works/add', [
    'App\Controllers\Sells\SellOffersController',
    'addWorksSellOffersAction'
]);
$map->get('editWorksSellOffers', '/intranet/crm/offers/works/edit', [
    'App\Controllers\Sells\SellOffersController',
    'editWorksSellOffersAction'
]);
$map->get('delWorksSellOffers', '/intranet/crm/offers/works/del', [
    'App\Controllers\Sells\SellOffersController',
    'delWorksSellOffersAction'
]);
$map->post('saveSellOffers', '/intranet/crm/offers/save', [
    'App\Controllers\Sells\SellOffersController',
    'getSellOffersDataAction'
]);
$map->get('deleteSellOffers', '/intranet/crm/offers/delete', [        
    'App\Controllers\Sells\SellOffersController',
>>>>>>> RepairMigrations
    'deleteAction'   
]);
$map->get('sellDeliveriesForm', '/sells/sellDeliveries/form', [
    'App\Controllers\Sells\SellDeliveriesController',
    'getSellDeliveriesDataAction'
]);
$map->get('sellDeliveriesList', '/sells/sellDeliveries/list', [
    'App\Controllers\Sells\SellDeliveriesController',
    'getIndexAction'
]);
$map->post('searchSellDeliveries', '/sells/sellDeliveries/search', [
    'App\Controllers\Sells\SellDeliveriesController',
    'searchSellDeliveriesAction'
]);
$map->post('saveSellDeliveries', '/sells/sellDeliveries/save', [
    'App\Controllers\Sells\SellDeliveriesController',
    'getSellDeliveriesDataAction'
]);
$map->get('deleteSellDeliveries', '/sells/sellDeliveries/delete', [        
    'App\Controllers\Sells\SellDeliveriesController',
    'deleteAction'  
]);
$map->get('sellInvoicesForm', '/sells/invoices/form', [
    'App\Controllers\Sells\SellInvoicesController',
    'getSellInvoicesDataAction'
]);
$map->get('sellInvoicesList', '/sells/invoices/list', [
    'App\Controllers\Sells\SellInvoicesController',
    'getIndexAction'
]);
$map->post('searchSellInvoices', '/sells/invoices/search', [
    'App\Controllers\Sells\SellInvoicesController',
    'searchSellInvoicesAction'
]);
$map->post('saveSellInvoices', '/sells/invoices/save', [
    'App\Controllers\Sells\SellInvoicesController',
    'getSellInvoicesDataAction'
]);
$map->get('deleteSellInvoices', '/sells/invoices/delete', [        
    'App\Controllers\Sells\SellInvoicesController',
    'deleteAction'  
]);
/*
 * BUYS ROUTES
 */

<<<<<<< HEAD
$map->get('vehicleForm', '/vehicles/form', [
    'App\Controllers\Buys\VehicleController',
    'getVehicleDataAction'
]);
$map->get('vehicleList', '/vehicles/list', [
    'App\Controllers\Buys\VehicleController',
    'getIndexAction'
]);
$map->post('searchVehicle', '/vehicles/search', [
    'App\Controllers\Buys\VehicleController',
    'searchVehicleAction'
]);
$map->post('importVehicles', '/vehicles/import', [
    'App\Controllers\Buys\VehicleController',
    'importExcel'
]);
$map->post('saveVehicle', '/vehicles/save', [
    'App\Controllers\Buys\VehicleController',
    'getVehicleDataAction'
]);
$map->get('deleteVehicle', '/vehicles/delete', [        
    'App\Controllers\Buys\VehicleController',
    'deleteAction'   
]);
$map->get('accesoryForm', '/vehicles/accesories/form', [
    'App\Controllers\Buys\AccesoriesController',
    'getAccesoryDataAction'
]);
$map->get('accesoryList', '/vehicles/accesories/list', [
    'App\Controllers\Buys\AccesoriesController',
    'getIndexAction'
]);
$map->post('searchAccesory', '/vehicles/accesories/search', [
    'App\Controllers\Buys\AccesoriesController',
    'searchAccesoryAction'
]);
$map->post('saveAccsory', '/vehicles/accesories/save', [
    'App\Controllers\Buys\AccesoriesController',
    'getAccesoryDataAction'
]);
$map->get('deleteAccesory', '/vehicles/accesories/delete', [        
    'App\Controllers\Buys\AccesoryController',
    'deleteAction'   
]);
$map->post('addVehicleAccesory', '/vehicles/accesories/add', [        
    'App\Controllers\Buys\VehicleController',
    'addAccesoryAction'   
]);
$map->post('deleteVehicleAccesory', '/vehicles/accesories/del', [        
    'App\Controllers\Buys\VehicleController',
    'deleteAccesoryAction'   
]);
$map->get('brandForm', '/vehicles/brands/form', [
    'App\Controllers\Entitys\BrandController',
    'getBrandDataAction'
]);
$map->get('brandList', '/vehicles/brands/list', [
    'App\Controllers\Entitys\BrandController',
    'getIndexAction'
]);
$map->post('searchBrand', '/vehicles/brands/search', [
    'App\Controllers\Entitys\BrandController',
    'searchBrandAction'
]);
$map->post('saveBrand', '/vehicles/brands/save', [
    'App\Controllers\Entitys\BrandController',
    'getBrandDataAction'
]);
$map->get('deleteBrand', '/vehicles/brands/delete', [        
    'App\Controllers\Entitys\BrandController',
    'deleteAction'   
]);
$map->get('modelForm', '/vehicles/models/form', [
    'App\Controllers\Entitys\ModelController',
    'getModelDataAction'
]);
$map->get('modelList', '/vehicles/models/list', [
    'App\Controllers\Entitys\ModelController',
    'getIndexAction'
]);
$map->post('searchModel', '/vehicles/models/search', [
    'App\Controllers\Entitys\ModelController',
    'searchModelAction'
]);
$map->post('saveModel', '/vehicles/models/save', [
    'App\Controllers\Entitys\ModelController',
    'getModelDataAction'
]);
$map->get('deleteModel', '/vehicles/models/delete', [        
    'App\Controllers\Entitys\ModelController',
    'deleteAction'   
]);
$map->get('WorksForm', '/vehicles/works/form', [
    'App\Controllers\Buys\WorksController',
    'getWorkDataAction'
]);
$map->get('WorksList', '/vehicles/works/list', [
    'App\Controllers\Buys\WorksController',
    'getIndexAction'
]);
$map->post('searchWorks', '/vehicles/works/search', [
    'App\Controllers\Buys\WorksController',
    'searchWorksAction'
]);
$map->post('saveWorks', '/vehicles/works/save', [
    'App\Controllers\Buys\WorksController',
    'getWorkDataAction'
]);
$map->get('deleteWorks', '/vehicles/works/delete', [        
    'App\Controllers\Buys\WorksController',
    'deleteAction'   
]);
$map->get('vehicleTypesForm', '/vehicles/vehicleTypes/form', [
    'App\Controllers\Buys\VehicleTypesController',
    'getVehicleTypesDataAction'
]);
$map->get('vehicleTypesList', '/vehicles/vehicleTypes/list', [
    'App\Controllers\Buys\VehicleTypesController',
    'getIndexAction'
]);
$map->post('searchVehicleTypes', '/vehicles/vehicleTypes/search', [
    'App\Controllers\Buys\VehicleTypesController',
    'searchVehicleTypesAction'
]);
$map->post('saveVehicleTypes', '/vehicles/vehicleTypes/save', [
    'App\Controllers\Buys\VehicleTypesController',
    'getVehicleTypesDataAction'
]);
$map->get('deleteVehicleTypes', '/vehicles/vehicleTypes/delete', [        
    'App\Controllers\Buys\VehicleTypesController',
=======
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
    'App\Controllers\Vehicles\AccesoriesController',
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
>>>>>>> RepairMigrations
    'deleteAction'   
]);
$map->get('deliveriesForm', '/buys/buyDeliveries/form', [
    'App\Controllers\Buys\BuyDeliveriesController',
    'getBuyDeliveriesDataAction'
]);
$map->get('deliveriesList', '/buys/buyDeliveries/list', [
    'App\Controllers\Buys\BuyDeliveriesController',
    'getIndexAction'
]);
$map->post('searchDeliveries', '/buys/buyDeliveries/search', [
    'App\Controllers\Buys\BuyDeliveriesController',
    'searchBuyDeliveriesAction'
]);
$map->post('saveDeliveries', '/buys/buyDeliveries/save', [
    'App\Controllers\Buys\BuyDeliveriesController',
    'getBuyDeliveriesDataAction'
]);
$map->get('deleteDeliveries', '/buys/buyDeliveries/delete', [        
    'App\Controllers\Buys\BuyDeliveriesController',
    'deleteAction'  
]);
$map->get('buyInvoicesForm', '/buys/buyInvoices/form', [
    'App\Controllers\Buys\BuyInvoicesController',
    'getBuyInvoicesDataAction'
]);
$map->get('buyInvoicesList', '/buys/buyInvoices/list', [
    'App\Controllers\Buys\BuyInvoicesController',
    'getIndexAction'
]);
$map->post('searchBuyInvoices', '/buys/buyInvoices/search', [
    'App\Controllers\Buys\BuyInvoicesController',
    'searchBuyInvoicesAction'
]);
$map->post('saveBuyInvoices', '/buys/buyInvoices/save', [
    'App\Controllers\Buys\BuyInvoicesController',
    'getBuyInvoicesDataAction'
]);
$map->get('deleteBuyInvoices', '/buys/buyInvoices/delete', [        
    'App\Controllers\Buys\BuyInvoicesController',
    'deleteAction'  
]);
<<<<<<< HEAD
$map->get('componentsForm', '/buys/components/form', [
    'App\Controllers\Buys\ComponentsController',
    'getComponentsDataAction'
]);
$map->get('componentsList', '/buys/components/list', [
    'App\Controllers\Buys\ComponentsController',
    'getIndexAction'
]);
$map->post('searchComponents', '/buys/components/search', [
    'App\Controllers\Buys\ComponentsController',
    'searchComponentsAction'
]);

$map->post('saveComponents', '/buys/components/save', [
    'App\Controllers\Buys\ComponentsController',
    'getComponentsDataAction'
]);
$map->get('deleteComponents', '/buys/components/delete', [        
    'App\Controllers\Buys\ComponentsController',
    'deleteAction'  
]);
$map->get('suppliesForm', '/buys/supplies/form', [
    'App\Controllers\Buys\SuppliesController',
    'getSuppliesDataAction'
]);
$map->get('suppliesList', '/buys/supplies/list', [
    'App\Controllers\Buys\SuppliesController',
    'getIndexAction'
]);
$map->post('searchSupplies', '/buys/supplies/search', [
    'App\Controllers\Buys\SuppliesController',
    'searchSuppliesAction'
]);
$map->post('saveSupplies', '/buys/supplies/save', [
    'App\Controllers\Buys\SuppliesController',
    'getSuppliesDataAction'
]);
$map->get('deleteSupplies', '/buys/supplies/delete', [        
    'App\Controllers\Buys\SuppliesController',
=======
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
>>>>>>> RepairMigrations
    'deleteAction'  
]);
$map->get('providersList', '/buys/providers/form', [
    'App\Controllers\Buys\ProvidersController',
    'getProviderDataAction'
]);
$map->get('providersForm', '/buys/providers/list', [
    'App\Controllers\Buys\ProvidersController',
    'getIndexAction'
]);
$map->post('searchProvider', '/buys/providers/search', [
    'App\Controllers\Buys\ProvidersController',
    'searchProviderAction'
]);
$map->post('saveProvider', '/buys/providers/save', [
    'App\Controllers\Buys\ProvidersController',
    'getProviderDataAction'
]);
$map->get('providersDelete', '/buys/providers/delete', [
    'App\Controllers\Buys\ProvidersController',
    'deleteAction'
]);
$map->get('madersList', '/buys/maders/form', [
    'App\Controllers\Buys\MadersController',
    'getMaderDataAction'
]);
$map->get('madersForm', '/buys/maders/list', [
    'App\Controllers\Buys\MadersController',
    'getIndexAction'    
]);
$map->post('saveMader', '/buys/maders/save', [
    'App\Controllers\Buys\MadersController',
    'getMaderDataAction' 
]);
$map->get('madersDelete', '/buys/maders/delete', [
    'App\Controllers\Buys\MadersController',
    'deleteAction'
]);
$map->get('ordersList', '/buys/orders/form', [
    'App\Controllers\Buys\GarageOrdersController',
    'getOrderDataAction'
]);
$map->get('ordersForm', '/buys/orders/list', [
    'App\Controllers\Buys\GarageOrdersController',
    'getIndexAction'
]);
$map->post('saveWork', '/buys/orders/form/work/save', [
    'App\Controllers\Buys\GarageOrdersController',
    'getOrderDataAction'
]);
$map->post('saveOrder', '/buys/orders/save', [
    'App\Controllers\Buys\GarageOrdersController',
    'getOrderDataAction'
]);
$map->get('ordersDelete', '/buys/orders/delete', [
    'App\Controllers\Buys\GarageOrdersController',
    'deleteAction'
]);
/*
 * PRODUCTION ROUTES
 */

$map->get('productionList', '/production/form', [
    'App\Controllers\Entitys\ProductionController',
    'getProductionDataAction'
]);
$map->get('productionForm', '/production/list', [
    'App\Controllers\Entitys\ProductionController',
    'getIndexAction'
]);
$map->post('saveProduction', '/production/save', [
    'App\Controllers\Entitys\ProductionController',
    'getProductionDataAction'
]);
$map->get('productionDelete', '/production/delete', [
    'App\Controllers\Entitys\ProductionController',
    'deleteAction'
]);

// REPORTS


$map->get('pruebaReport', '/reports/prueba', [
    'App\Controllers\PruebaReportController',
    'getReportAction'
]);
<<<<<<< HEAD
$map->post('SellOfferReport', '/reports/selloffer', [
    'App\Controllers\Crm\SellOffersController',
    'getReportAction'
=======
$map->post('SellOfferDetailedReport', '/intranet/reports/sellofferDetailed', [
    'App\Controllers\Sells\SellOffersController',
    'getDetailedReportAction'
]);
$map->post('SellOfferVehicleReport', '/intranet/reports/sellofferVehicle', [
    'App\Controllers\Sells\SellOffersController',
    'getVehicleReportAction'
]);
$map->post('SellOfferVehicleExportReport', '/intranet/reports/sellofferVehicleExport', [
    'App\Controllers\Sells\SellOffersController',
    'getVehicleExportReportAction'
]);
$map->post('SellOfferVehicleIntraReport', '/intranet/reports/sellofferVehicleIntra', [
    'App\Controllers\Sells\SellOffersController',
    'getVehicleIntraReportAction'
>>>>>>> RepairMigrations
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
