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

$map->get('companyForm', '/company/form', [
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
    'App\Controllers\Sells\SellersController',
    'getSellersDataAction'
]);
$map->get('sellerForm', '/sellers/list', [
    'App\Controllers\Sells\SellersController',
    'getIndexAction'
]);
$map->post('saveSeller', '/sellers/save', [
    'App\Controllers\Sells\SellersController',
    'getSellersDataAction'
]);
$map->get('sellerDelete', '/sellers/delete', [
    'App\Controllers\Sells\SellersController',
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
    'App\Controllers\Sells\FinanceController',
    'getFinanceDataAction'
]);
$map->get('financeForm', '/finance/list', [
    'App\Controllers\Sells\FinanceController',
    'getIndexAction'
    
]);
$map->post('saveFinance', '/finance/save', [
    'App\Controllers\Sells\FinanceController',
    'getFinanceDataAction'
]);
$map->get('financeDelete', '/finance/delete', [
    'App\Controllers\Sells\FinanceController',
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
    'App\Controllers\Vehicle\LocationController',
    'getLocationDataAction'
]);
$map->get('locationsForm', '/locations/list', [
    'App\Controllers\Vehicle\LocationController',
    'getIndexAction'    
]);
$map->post('searchLocations', '/locations/search', [
    'App\Controllers\Vehicle\LocationController',
    'searchLocationAction'
]);
$map->post('saveLocation', '/locations/save', [
    'App\Controllers\Vehicle\LocationController',
    'getLocationDataAction' 
]);
$map->get('locationsDelete', '/locations/delete', [
    'App\Controllers\Vehicle\LocationController',
    'deleteAction'
]);
$map->get('garagesList', '/buys/garages/form', [
    'App\Controllers\Garages\GaragesController',
    'getGarageDataAction'
]);
$map->get('garagesForm', '/buys/garages/list', [
    'App\Controllers\Garages\GaragesController',
    'getIndexAction'
]);
$map->post('searchGarage', '/buys/garages/search', [
    'App\Controllers\Garages\GaragesController',
    'searchGarageAction'
]);
$map->post('saveGarage', '/buys/garages/save', [
    'App\Controllers\Garages\GaragesController',
    'getGarageDataAction'
]);
$map->get('garagesDelete', '/buys/garages/delete', [
    'App\Controllers\Garages\GaragesController',
    'deleteAction'
]);
$map->get('paymentWaysList', '/paymentWays/form', [
    'App\Controllers\Entitys\PaymentWaysController',
    'getPaymentWaysDataAction'
]);
$map->get('paymentWaysForm', '/paymentWays/list', [
    'App\Controllers\Entitys\PaymentWaysController',
    'getIndexAction'
]);
$map->post('savePaymentWays', '/paymentWays/save', [
    'App\Controllers\Entitys\PaymentWaysController',
    'getPaymentWaysDataAction'
]);
$map->get('paymentWaysDelete', '/paymentWays/delete', [
    'App\Controllers\Entitys\PaymentWaysController',
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

$map->get('customerForm', '/customers/form', [
    'App\Controllers\Sells\CustomerController',
    'getCustomerDataAction'
]);
$map->get('customerList', '/customers/list', [
    'App\Controllers\Sells\CustomerController',
    'getIndexAction'
]);
$map->post('searchCustomer', '/customers/search', [
    'App\Controllers\Sells\CustomerController',
    'searchCustomerAction'
]);
$map->post('saveCustomer', '/customers/save', [
    'App\Controllers\Sells\CustomerController',
    'getCustomerDataAction'
]);
$map->get('deleteCustomer', '/customers/delete', [        
    'App\Controllers\Sells\CustomerController',
    'deleteAction'   
]);
$map->get('customerTypeForm', '/customers/type/form', [
    'App\Controllers\Sells\CustomerTypesController',
    'getCustomerTypesDataAction'
]);
$map->get('customerTypeList', '/customers/type/list', [
    'App\Controllers\Sells\CustomerTypesController',
    'getIndexAction'
]);
$map->post('searchCustomerType', '/customers/type/search', [
    'App\Controllers\Sells\CustomerTypesController',
    'searchCustomerTypeAction'
]);
$map->post('saveCustomerType', '/customers/type/save', [
    'App\Controllers\Sells\CustomerTypesController',
    'getCustomerTypesDataAction'
]);
$map->get('deleteCustomerType', '/customers/type/delete', [        
    'App\Controllers\Sells\CustomerTypesController',
    'deleteAction'   
]);
$map->get('sellOffersForm', '/sells/offers/form', [
    'App\Controllers\Sells\SellOffersController',
    'getSellOffersDataAction'
]);
$map->get('sellOffersList', '/sells/offers/list', [
    'App\Controllers\Sells\SellOffersController',
    'getIndexAction'
]);
$map->post('searchSellOffers', '/sells/offers/search', [
    'App\Controllers\Sells\SellOffersController',
    'searchSellOffersAction'
]);
$map->post('searchCustomerSellOffers', '/sells/offers/customer/search', [
    'App\Controllers\Sells\SellOffersController',
    'searchCustomerSellOfferAction'
]);
$map->get('selectCustomerSellOffers', '/sells/offers/customer/select', [
    'App\Controllers\Sells\SellOffersController',
    'selectCustomerSellOfferAction'
]);
$map->post('searchVehicleSellOffers', '/sells/offers/vehicle/search', [
    'App\Controllers\Sells\SellOffersController',
    'searchVehicleSellOfferAction'
]);
$map->get('selectVehicleSellOffers', '/sells/offers/vehicle/select', [
    'App\Controllers\Sells\SellOffersController',
    'selectVehicleSellOfferAction'
]);
$map->post('searchComponentsSellOffers', '/sells/offers/components/search', [
    'App\Controllers\Sells\SellOffersController',
    'searchComponentsSellOffersAction'
]);
$map->get('selectComponentsSellOffers', '/sells/offers/components/select', [
    'App\Controllers\Sells\SellOffersController',
    'selectComponentsSellOffersAction'
]);
$map->post('addComponentsSellOffers', '/sells/offers/components/add', [
    'App\Controllers\Sells\SellOffersController',
    'addComponentsSellOffersAction'
]);
$map->get('editComponentsSellOffers', '/sells/offers/components/edit', [
    'App\Controllers\Sells\SellOffersController',
    'editComponentsSellOffersAction'
]);
$map->get('delComponentsSellOffers', '/sells/offers/components/del', [
    'App\Controllers\Sells\SellOffersController',
    'delComponentsSellOffersAction'
]);
$map->post('searchSuppliesSellOffers', '/sells/offers/supplies/search', [
    'App\Controllers\Sells\SellOffersController',
    'searchSuppliesSellOffersAction'
]);
$map->get('selectSuppliesSellOffers', '/sells/offers/supplies/select', [
    'App\Controllers\Sells\SellOffersController',
    'selectSuppliesSellOffersAction'
]);
$map->post('addSuppliesSellOffers', '/sells/offers/supplies/add', [
    'App\Controllers\Sells\SellOffersController',
    'addSuppliesSellOffersAction'
]);
$map->get('editSuppliesSellOffers', '/sells/offers/supplies/edit', [
    'App\Controllers\Sells\SellOffersController',
    'editSuppliesSellOffersAction'
]);
$map->get('delSuppliesSellOffers', '/sells/offers/supplies/del', [
    'App\Controllers\Sells\SellOffersController',
    'delSuppliesSellOffersAction'
]);
$map->post('searchWorksSellOffers', '/sells/offers/works/search', [
    'App\Controllers\Sells\SellOffersController',
    'searchWorksSellOffersAction'
]);
$map->get('selectWorksSellOffers', '/sells/offers/works/select', [
    'App\Controllers\Sells\SellOffersController',
    'selectWorksSellOffersAction'
]);
$map->post('addWorksSellOffers', '/sells/offers/works/add', [
    'App\Controllers\Sells\SellOffersController',
    'addWorksSellOffersAction'
]);
$map->get('editWorksSellOffers', '/sells/offers/works/edit', [
    'App\Controllers\Sells\SellOffersController',
    'editWorksSellOffersAction'
]);
$map->get('delWorksSellOffers', '/sells/offers/works/del', [
    'App\Controllers\Sells\SellOffersController',
    'delWorksSellOffersAction'
]);
$map->post('saveSellOffers', '/sells/offers/save', [
    'App\Controllers\Sells\SellOffersController',
    'getSellOffersDataAction'
]);
$map->get('deleteSellOffers', '/sells/offers/delete', [        
    'App\Controllers\Sells\SellOffersController',
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

$map->get('vehicleForm', '/vehicles/form', [
    'App\Controllers\Vehicle\VehicleController',
    'getVehicleDataAction'
]);
$map->get('vehicleList', '/vehicles/list', [
    'App\Controllers\Vehicle\VehicleController',
    'getIndexAction'
]);
$map->post('searchVehicle', '/vehicles/search', [
    'App\Controllers\Vehicle\VehicleController',
    'searchVehicleAction'
]);
$map->post('importVehicles', '/vehicles/import', [
    'App\Controllers\Vehicle\VehicleController',
    'importVehiclesExcel'
]);
$map->post('saveVehicle', '/vehicles/save', [
    'App\Controllers\Vehicle\VehicleController',
    'getVehicleDataAction'
]);
$map->get('deleteVehicle', '/vehicles/delete', [        
    'App\Controllers\Vehicle\VehicleController',
    'deleteAction'   
]);
$map->get('accesoryForm', '/vehicles/accesories/form', [
    'App\Controllers\Vehicle\AccesoriesController',
    'getAccesoryDataAction'
]);
$map->get('accesoryList', '/vehicles/accesories/list', [
    'App\Controllers\Vehicle\AccesoriesController',
    'getIndexAction'
]);
$map->post('searchAccesory', '/vehicles/accesories/search', [
    'App\Controllers\Vehicle\AccesoriesController',
    'searchAccesoryAction'
]);
$map->post('saveAccsory', '/vehicles/accesories/save', [
    'App\Controllers\Vehicle\AccesoriesController',
    'getAccesoryDataAction'
]);
$map->get('deleteAccesory', '/vehicles/accesories/delete', [        
    'App\Controllers\Vehicle\AccesoriesController',
    'deleteAction'   
]);
$map->post('addVehicleAccesory', '/vehicles/accesories/add', [        
    'App\Controllers\Vehicle\VehicleController',
    'addAccesoryAction'   
]);
$map->post('deleteVehicleAccesory', '/vehicles/accesories/del', [        
    'App\Controllers\Vehicle\VehicleController',
    'deleteAccesoryAction'   
]);
$map->get('brandForm', '/vehicles/brands/form', [
    'App\Controllers\Vehicle\BrandController',
    'getBrandDataAction'
]);
$map->get('brandList', '/vehicles/brands/list', [
    'App\Controllers\Vehicle\BrandController',
    'getIndexAction'
]);
$map->post('searchBrand', '/vehicles/brands/search', [
    'App\Controllers\Vehicle\BrandController',
    'searchBrandAction'
]);
$map->post('saveBrand', '/vehicles/brands/save', [
    'App\Controllers\Vehicle\BrandController',
    'getBrandDataAction'
]);
$map->get('deleteBrand', '/vehicles/brands/delete', [        
    'App\Controllers\Vehicle\BrandController',
    'deleteAction'   
]);
$map->get('modelForm', '/vehicles/models/form', [
    'App\Controllers\Vehicle\ModelController',
    'getModelDataAction'
]);
$map->get('modelList', '/vehicles/models/list', [
    'App\Controllers\Vehicle\ModelController',
    'getIndexAction'
]);
$map->post('searchModel', '/vehicles/models/search', [
    'App\Controllers\Vehicle\ModelController',
    'searchModelAction'
]);
$map->post('saveModel', '/vehicles/models/save', [
    'App\Controllers\Vehicle\ModelController',
    'getModelDataAction'
]);
$map->get('deleteModel', '/vehicles/models/delete', [        
    'App\Controllers\Vehicle\ModelController',
    'deleteAction'   
]);
$map->get('WorksForm', '/vehicles/works/form', [
    'App\Controllers\Vehicle\WorksController',
    'getWorkDataAction'
]);
$map->get('WorksList', '/vehicles/works/list', [
    'App\Controllers\Vehicle\WorksController',
    'getIndexAction'
]);
$map->post('searchWorks', '/vehicles/works/search', [
    'App\Controllers\Vehicle\WorksController',
    'searchWorksAction'
]);
$map->post('saveWorks', '/vehicles/works/save', [
    'App\Controllers\Vehicle\WorksController',
    'getWorkDataAction'
]);
$map->get('deleteWorks', '/vehicles/works/delete', [        
    'App\Controllers\Vehicle\WorksController',
    'deleteAction'   
]);
$map->get('vehicleTypesForm', '/vehicles/vehicleTypes/form', [
    'App\Controllers\Vehicle\VehicleTypesController',
    'getVehicleTypesDataAction'
]);
$map->get('vehicleTypesList', '/vehicles/vehicleTypes/list', [
    'App\Controllers\Vehicle\VehicleTypesController',
    'getIndexAction'
]);
$map->post('searchVehicleTypes', '/vehicles/vehicleTypes/search', [
    'App\Controllers\Vehicle\VehicleTypesController',
    'searchVehicleTypesAction'
]);
$map->post('saveVehicleTypes', '/vehicles/vehicleTypes/save', [
    'App\Controllers\Vehicle\VehicleTypesController',
    'getVehicleTypesDataAction'
]);
$map->get('deleteVehicleTypes', '/vehicles/vehicleTypes/delete', [        
    'App\Controllers\Vehicle\VehicleTypesController',
    'deleteAction'   
]);
$map->get('deliveriesForm', '/Buys/buyDeliveries/form', [
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
$map->get('componentsForm', '/buys/components/form', [
    'App\Controllers\Vehicle\ComponentsController',
    'getComponentsDataAction'
]);
$map->get('componentsList', '/buys/components/list', [
    'App\Controllers\Vehicle\ComponentsController',
    'getIndexAction'
]);
$map->post('searchComponents', '/buys/components/search', [
    'App\Controllers\Vehicle\ComponentsController',
    'searchComponentsAction'
]);

$map->post('saveComponents', '/buys/components/save', [
    'App\Controllers\Vehicle\ComponentsController',
    'getComponentsDataAction'
]);
$map->get('deleteComponents', '/buys/components/delete', [        
    'App\Controllers\Vehicle\ComponentsController',
    'deleteAction'  
]);
$map->get('suppliesForm', '/buys/supplies/form', [
    'App\Controllers\Vehicle\SuppliesController',
    'getSuppliesDataAction'
]);
$map->get('suppliesList', '/buys/supplies/list', [
    'App\Controllers\Vehicle\SuppliesController',
    'getIndexAction'
]);
$map->post('searchSupplies', '/buys/supplies/search', [
    'App\Controllers\Vehicle\SuppliesController',
    'searchSuppliesAction'
]);
$map->post('saveSupplies', '/buys/supplies/save', [
    'App\Controllers\Vehicle\SuppliesController',
    'getSuppliesDataAction'
]);
$map->get('deleteSupplies', '/buys/supplies/delete', [        
    'App\Controllers\Vehicle\SuppliesController',
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
    'App\Controllers\Garages\GarageOrdersController',
    'getOrderDataAction'
]);
$map->get('ordersForm', '/buys/orders/list', [
    'App\Controllers\Garages\GarageOrdersController',
    'getIndexAction'
]);
$map->post('saveWork', '/buys/orders/form/work/save', [
    'App\Controllers\Garages\GarageOrdersController',
    'getOrderDataAction'
]);
$map->post('saveOrder', '/buys/orders/save', [
    'App\Controllers\Garages\GarageOrdersController',
    'getOrderDataAction'
]);
$map->get('ordersDelete', '/buys/orders/delete', [
    'App\Controllers\Garages\GarageOrdersController',
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
$map->post('SellOfferReport', '/reports/selloffer', [
    'App\Controllers\Sells\SellOffersController',
    'getReportAction'
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
