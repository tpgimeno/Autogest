<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace App\Controllers\Garages;

use App\Controllers\BaseController;
use App\Models\GarageOrder;
use App\Services\Garages\PlaningService;

/**
 * Description of PlanningController
 *
 * @author tonyl
 */
class PlanningController extends BaseController{
    protected $planningService;
    public function __construct(PlaningService $planningService) {
        parent::__construct();
        $this->planningService = $planningService;
    }
    public function getIndexAction($request){
        $params = $request->getQueryParams();
        $iterables = ['orders' => $this->planningService->getAllRegisters(new GarageOrder)
                ];
        $menuState = $params['menu'];  
        $menuItem = $params['item'];
        return $this->renderHTML('/planning.html.twig', [ 
                    'optionsArray' => $iterables,
                    'menuState' => $menuState,
                    'menuItem' => $menuItem
        ]);
    }
    
    
    
}
