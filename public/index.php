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

$map->get('index', '/intranet/', [
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

$map->get('companyForm', '/intranet/company/form', [
    'App\Controllers\Entitys\CompanyController',
    'getCompanyDataAction'
]);
$map->get('companyList', '/intranet/company/list', [
    'App\Controllers\Entitys\CompanyController',
    'getIndexAction'
]);
$map->post('saveCompany', '/intranet/company/save', [
    'App\Controllers\Entitys\CompanyController',
    'getCompanyDataAction'
]);
$map->get('deleteCompany', '/intranet/company/delete', [        
    'App\Controllers\Entitys\CompanyController',
    'deleteAction'  
]);
$map->get('customerForm', '/intranet/customers/form', [
    'App\Controllers\Entitys\CustomerController',
    'getCustomerDataAction'
]);
$map->get('customerList', '/intranet/customers/list', [
    'App\Controllers\Entitys\CustomerController',
    'getIndexAction'
]);
$map->post('searchCustomer', '/intranet/customers/search', [
    'App\Controllers\Entitys\CustomerController',
    'searchCustomerAction'
]);
$map->post('saveCustomer', '/intranet/customers/save', [
    'App\Controllers\Entitys\CustomerController',
    'getCustomerDataAction'
]);
$map->get('deleteCustomer', '/intranet/customers/delete', [        
    'App\Controllers\Entitys\CustomerController',
    'deleteAction'   
]);
$map->get('vehicleForm', '/intranet/vehicles/form', [
    'App\Controllers\Buys\VehicleController',
    'getVehicleDataAction'
]);
$map->get('vehicleList', '/intranet/vehicles/list', [
    'App\Controllers\Buys\VehicleController',
    'getIndexAction'
]);
$map->post('searchVehicle', '/intranet/vehicles/search', [
    'App\Controllers\Buys\VehicleController',
    'searchVehicleAction'
]);
$map->post('saveVehicle', '/intranet/vehicles/save', [
    'App\Controllers\Buys\VehicleController',
    'getVehicleDataAction'
]);
$map->get('deleteVehicle', '/intranet/vehicles/delete', [        
    'App\Controllers\Buys\VehicleController',
    'deleteAction'   
]);
$map->get('brandForm', '/intranet/vehicles/brands/form', [
    'App\Controllers\Entitys\BrandController',
    'getBrandDataAction'
]);
$map->get('brandList', '/intranet/vehicles/brands/list', [
    'App\Controllers\Entitys\BrandController',
    'getIndexAction'
]);
$map->post('searchBrand', '/intranet/vehicles/brands/search', [
    'App\Controllers\Entitys\BrandController',
    'searchBrandAction'
]);
$map->post('saveBrand', '/intranet/vehicles/brands/save', [
    'App\Controllers\Entitys\BrandController',
    'getBrandDataAction'
]);
$map->get('deleteBrand', '/intranet/vehicles/brands/delete', [        
    'App\Controllers\Entitys\BrandController',
    'deleteAction'   
]);
$map->get('modelForm', '/intranet/vehicles/models/form', [
    'App\Controllers\Entitys\ModelController',
    'getModelDataAction'
]);
$map->get('modelList', '/intranet/vehicles/models/list', [
    'App\Controllers\Entitys\ModelController',
    'getIndexAction'
]);
$map->post('searchModel', '/intranet/vehicles/models/search', [
    'App\Controllers\Entitys\ModelController',
    'searchModelAction'
]);
$map->post('saveModel', '/intranet/vehicles/models/save', [
    'App\Controllers\Entitys\ModelController',
    'getModelDataAction'
]);
$map->get('deleteModel', '/intranet/vehicles/models/delete', [        
    'App\Controllers\Entitys\ModelController',
    'deleteAction'   
]);
$map->get('vehicleTypesForm', '/intranet/vehicles/vehicleTypes/form', [
    'App\Controllers\Buys\VehicleTypesController',
    'getVehicleTypesDataAction'
]);
$map->get('vehicleTypesList', '/intranet/vehicles/vehicleTypes/list', [
    'App\Controllers\Buys\VehicleTypesController',
    'getIndexAction'
]);
$map->post('searchVehicleTypes', '/intranet/vehicles/vehicleTypes/search', [
    'App\Controllers\Buys\VehicleTypesController',
    'searchVehicleTypesAction'
]);
$map->post('saveVehicleTypes', '/intranet/vehicles/vehicleTypes/save', [
    'App\Controllers\Buys\VehicleTypesController',
    'getVehicleTypesDataAction'
]);
$map->get('deleteVehicleTypes', '/intranet/vehicles/vehicleTypes/delete', [        
    'App\Controllers\Buys\VehicleTypesController',
    'deleteAction'   
]);
$map->get('sellOffersForm', '/intranet/crm/offers/form', [
    'App\Controllers\Crm\SellOffersController',
    'getSellOfferDataAction'
]);
$map->get('sellOffersList', '/intranet/crm/offers/list', [
    'App\Controllers\Crm\SellOffersController',
    'getIndexAction'
]);
$map->post('searchSellOffers', '/intranet/crm/offers/search', [
    'App\Controllers\Crm\SellOffersController',
    'searchSellOffersAction'
]);
$map->post('searchCustomerSellOffers', '/intranet/crm/offers/customer/search', [
    'App\Controllers\Crm\SellOffersController',
    'searchCustomerSellOfferAction'
]);
$map->get('selectCustomerSellOffers', '/intranet/crm/offers/customer/search', [
    'App\Controllers\Crm\SellOffersController',
    'searchCustomerSellOfferAction'
]);
$map->post('searchVehicleSellOffers', '/intranet/crm/offers/vehicle/search', [
    'App\Controllers\Crm\SellOffersController',
    'searchVehicleSellOfferAction'
]);
$map->post('saveSellOffers', '/intranet/crm/offers/save', [
    'App\Controllers\Crm\SellOffersController',
    'getSellOffersDataAction'
]);
$map->get('deleteSellOffers', '/intranet/crm/offers/delete', [        
    'App\Controllers\Crm\SellOffersController',
    'deleteAction'   
]);
$map->get('userList', '/intranet/users/form', [
    'App\Controllers\UsersController',
    'getAddUserAction'
]);
$map->get('userForm', '/intranet/users/list', [
    'App\Controllers\UsersController',
    'getIndexUsers'
]);
$map->post('saveUser', '/intranet/users/form', [
    'App\Controllers\UsersController',
    'getAddUserAction'
]);
$map->get('userDelete', '/intranet/users/delete', [
    'App\Controllers\UsersController',
    'deleteAction'
]);
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
$map->get('financeList', '/intranet/finance/form', [
    'App\Controllers\Entitys\FinanceController',
    'getFinanceDataAction'
]);
$map->get('financeForm', '/intranet/finance/list', [
    'App\Controllers\Entitys\FinanceController',
    'getIndexAction'
    
]);
$map->post('saveFinance', '/intranet/finance/save', [
    'App\Controllers\Entitys\FinanceController',
    'getFinanceDataAction'
]);
$map->get('financeDelete', '/intranet/finance/delete', [
    'App\Controllers\Entitys\FinanceController',
    'deleteAction'
    
]);
$map->get('storesList', '/intranet/stores/form', [
    'App\Controllers\Entitys\StoreController',
    'getStoreDataAction'
]);
$map->get('storesForm', '/intranet/stores/list', [
    'App\Controllers\Entitys\StoreController',
    'getIndexAction'
    
]);
$map->post('saveStore', '/intranet/stores/save', [
    'App\Controllers\Entitys\StoreController',
    'getStoreDataAction' 
]);
$map->get('storesDelete', '/intranet/stores/delete', [
    'App\Controllers\Entitys\StoreController',
    'deleteAction'
]);
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
$map->get('providersList', '/intranet/buys/providers/form', [
    'App\Controllers\Buys\GaragesController',
    'getProviderDataAction'
]);
$map->get('providersForm', '/intranet/buys/providers/list', [
    'App\Controllers\Buys\ProvidersController',
    'getIndexAction'
]);
$map->post('saveProvider', '/intranet/buys/providers/save', [
    'App\Controllers\Buys\ProvidersController',
    'getProviderDataAction'
]);
$map->get('providersDelete', '/intranet/buys/providers/delete', [
    'App\Controllers\Buys\ProvidersController',
    'deleteAction'
]);
$map->get('garagesList', '/intranet/buys/garages/form', [
    'App\Controllers\Buys\GaragesController',
    'getGarageDataAction'
]);
$map->get('garagesForm', '/intranet/buys/garages/list', [
    'App\Controllers\Buys\GaragesController',
    'getIndexAction'
]);
$map->post('saveGarage', '/intranet/buys/garages/save', [
    'App\Controllers\Buys\GaragesController',
    'getGarageDataAction'
]);
$map->get('garagesDelete', '/intranet/buys/garages/delete', [
    'App\Controllers\Buys\GaragesController',
    'deleteAction'
]);
$map->get('ordersList', '/intranet/buys/orders/form', [
    'App\Controllers\Buys\GarageOrdersController',
    'getOrderDataAction'
]);
$map->get('ordersForm', '/intranet/buys/orders/list', [
    'App\Controllers\Buys\GarageOrdersController',
    'getIndexAction'
]);
$map->post('saveWork', '/intranet/buys/orders/form/work/save', [
    'App\Controllers\Buys\GarageOrdersController',
    'getOrderDataAction'
]);
$map->post('saveOrder', '/intranet/buys/orders/save', [
    'App\Controllers\Buys\GarageOrdersController',
    'getOrderDataAction'
]);
$map->get('ordersDelete', '/intranet/buys/orders/delete', [
    'App\Controllers\Buys\GarageOrdersController',
    'deleteAction'
]);
$map->get('pruebaReport', '/intranet/reports/prueba', [
    'App\Controllers\PruebaReportController',
    'getReportAction'
]);
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
