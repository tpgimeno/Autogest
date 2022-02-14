<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace App\Routes;

use Aura\Router\RouterContainer;

/**
 * Description of ProductionRoutes
 *
 * @author tony
 */
class ProductionRoutes {
    public function getProductionRoutes()
    {
        $routerContainer = new RouterContainer();
        $map = $routerContainer->getMap();       
        
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
        return $routerContainer;
    }
}
