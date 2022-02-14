<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace App\Routes;

use Aura\Router\RouterContainer;

/**
 * Description of ReportsRoutes
 *
 * @author tony
 */
class ReportsRoutes {
    public function getReportsRoutes()
    {
        $routerContainer = new RouterContainer();
        $map = $routerContainer->getMap();
        
        // REPORTS

        $map->get('VehiclesReport', '/Intranet/reports/vehicles', [
            'App\Controllers\Vehicle\VehicleController',
            'getVehiclesReportAction'
        ]);
        $map->get('ComponentsReport', '/Intranet/reports/components', [
            'App\Controllers\Vehicle\ComponentsController',
            'getComponentsReportAction'
        ]);
        $map->get('SuppliesReport', '/Intranet/reports/supplies', [
            'App\Controllers\Vehicle\SuppliesController',
            'getSuppliesReportAction'
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
        
        return $routerContainer;
    }
}
