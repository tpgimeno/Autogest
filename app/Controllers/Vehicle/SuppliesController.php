<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Controllers\Vehicle;

use App\Controllers\BaseController;
use App\Models\Mader;
use App\Models\Supplies;
use App\Reports\Vehicles\SuppliesReport;
use App\Services\Vehicle\SuppliesService;
use Exception;
use Illuminate\Database\Capsule\Manager as DB;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Diactoros\ServerRequest;
use Respect\Validation\Validator as v;
/**
 * Description of SuppliesController
 *
 * @author tonyl
 */
class SuppliesController extends BaseController {
    protected $suppliesService;    
    public function __construct(SuppliesService $suppliesService) {
        parent::__construct();
        $this->suppliesService = $suppliesService;
    }
    public function getIndexAction() {
        $supplies = $this->suppliesService->getSupplies();       
        return $this->renderHTML('/vehicles/supplies/suppliesList.html.twig', [
            'supplies' => $supplies
        ]);
    }    
    public function getSuppliesDataAction($request) {
        $responseMessage = null;        
        if($request->getMethod() == 'POST') {
            $postData = $request->getParsedBody();
            $suppliesValidator = v::key('ref', v::stringType()->notEmpty())
                    ->key('maderCode', v::stringType())
                    ->key('name', v::stringType()->notEmpty());
            try{
                $suppliesValidator->assert($postData); 
                $postData['mader'] = $this->suppliesService->getMaderByName($postData);
                $responseMessage = $this->suppliesService->saveRegister(new Supplies(), $postData);
            } catch (Exception $ex) {
                $responseMessage = $ex->getMessage();
            }
        }        
        $maders = $this->suppliesService->getAllRegisters(new Mader());
        $selected_supply = $this->suppliesService->setSupplyInstance($request->getQueryParams('id'));    
        return $this->renderHTML('/vehicles/supplies/suppliesForm.html.twig', [
            'supply' => $selected_supply,
            'maders' => $maders,
            'responseMessage' => $responseMessage
        ]);
    }    
    public function deleteAction(ServerRequest $request) {         
        $this->suppliesService->deleteRegister(new Supplies(), $request->getQueryParams('id'));           
        return new RedirectResponse('/Intranet/vehicles/supplies/list');
    }
    
    
    public function getSuppliesReportAction(){
        $supplies = DB::table('supplies')  
                ->join('maders', 'supplies.mader', '=', 'maders.id')               
                ->select('supplies.id', 'supplies.name', 'supplies.ref', 'maders.name as mader', 'supplies.pvc', 'supplies.pvp' )               
                ->whereNull('supplies.deleted_at')                
                ->get()->toArray();
        
        $newPostData = array_merge(['supplies' => $supplies ]);
        $report = new SuppliesReport();
        $report->AddPage();
        $report->Body($newPostData);        
        $report->Output();
    }
}
