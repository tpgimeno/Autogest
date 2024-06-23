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
            'App\Controllers\Garages\ProductionController',
            'deleteAction'
        ]);
        
        $map->post('getWorkSheetNumber', '/Intranet/workSheets/number/get', [
            'App\Controllers\Garages\WorkSheetsController',
            'getWorkSheetsNumberAction'
        ]);
        $map->get('workSheetsList', '/Intranet/workSheets/form', [
            'App\Controllers\Garages\WorkSheetsController',
            'getWorkSheetsDataAction'
        ]);
        $map->get('workSheetsForm', '/Intranet/workSheets/list', [
            'App\Controllers\Garages\WorkSheetsController',
            'getIndexAction'
        ]);
        $map->post('saveWorkSheets', '/Intranet/workSheets/save', [
            'App\Controllers\Garages\WorkSheetsController',
            'getWorkSheetsDataAction'
        ]);
        $map->get('workSheetsDelete', '/Intranet/workSheets/delete', [
            'App\Controllers\Garages\WorkSheetsController',
            'deleteAction'
        ]);
        $map->post('saveWorkSheetComponent', '/Intranet/workSheets/components/add', [
           'App\Controllers\Garages\WorkSheetsController',
           'addComponentsWorkSheetsAction'
        ]);
        $map->post('delWorkSheetComponent', '/Intranet/workSheets/components/del', [
            'App\Controllers\Garages\WorkSheetsController',
            'delComponentsWorkSheetsAction'
        ]);
        $map->post('addWorkSheetSupply', '/Intranet/workSheets/supplies/add', [
            'App\Controllers\Garages\WorkSheetsController',
            'addSuppliesWorkSheetsAction'
        ]);
        $map->post('delWorkSheetSupply', '/Intranet/workSheets/supplies/del', [
            'App\Controllers\Garages\WorkSheetsController',
            'delSuppliesWorkSheetsAction'
        ]);
        $map->post('addWorkSheetWork', '/Intranet/workSheets/works/add', [
            'App\Controllers\Garages\WorkSheetsController',
            'addWorksWorkSheetsAction'
        ]);
        $map->post('delWorkSheetWork', '/Intranet/workSheets/works/del', [
            'App\Controllers\Garages\WorkSheetsController',
            'delWorksWorkSheetsAction'
        ]); 
        
        $map->get('expertOpinionsList', '/Intranet/expertOpinions/form', [
            'App\Controllers\Garages\ExpertOpinionsController',
            'getExpertOpinionsDataAction'
        ]);
        $map->get('expertOpinionsForm', '/Intranet/expertOpinions/list', [
            'App\Controllers\Garages\ExpertOpinionsController',
            'getIndexAction'
        ]);
        $map->post('saveExpertOpinions', '/Intranet/expertOpinions/save', [
            'App\Controllers\Garages\ExpertOpinionsController',
            'getExpertOpinionsDataAction'
        ]);
        $map->get('expertOpinionsDelete', '/Intranet/expertOpinions/delete', [
            'App\Controllers\Garages\ExpertOpinionsController',
            'deleteAction'
        ]);
        
        
        
        
        return $routerContainer;
    }
}
