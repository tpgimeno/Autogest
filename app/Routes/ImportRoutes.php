<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace App\Routes;

/**
 * Description of ImportRoutes
 *
 * @author tony
 */
class ImportRoutes {
   public function importRoutes()
   {
        $sellRoutes = new SellRoutes();
        $buyRoutes = new BuyRoutes();
        $genericRoutes = new GenericRoutes();
        $reportRoutes = new ReportsRoutes();        
        $routes = $genericRoutes->getGenericRoutes()->getMap();
        foreach ($sellRoutes->getSellRoutes()->getMap()->getRoutes() as $route)
        {
            $routes->addRoute($route);
        }
        foreach ($buyRoutes->getBuyRoutes()->getMap()->getRoutes() as $route)
        {
            $routes->addRoute($route);
        }
        foreach ($reportRoutes->getReportsRoutes()->getMap()->getRoutes() as $route)
        {
            $routes->addRoute($route);
        }
        return $routes;
   }
}
