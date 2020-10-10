<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Controllers\Buys;

use App\Controllers\BaseController;
use App\Models\Mader;
use App\Models\Supplies;
use App\Services\Buys\SuppliesService;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Diactoros\ServerRequest;
use ZipStream\Exception;
use Respect\Validation\Validator as v;
use Illuminate\Database\Capsule\Manager as DB;



/**
 * Description of SuppliesController
 *
 * @author tonyl
 */
class SuppliesController extends BaseController
{
    protected $suppliesService;
    
    public function __construct(SuppliesService $suppliesService) {
        parent::__construct();
        $this->suppliesService = $suppliesService;
    }
    public function getIndexAction()
    {
        $supplies = DB::table('supplies')
                ->join('maders', 'supplies.mader', '=', 'maders.id')
                ->select('supplies.id', 'supplies.ref', 'maders.name as mader', 'supplies.name', 'supplies.stock', 'supplies.mader_code', 'supplies.pvc', 'supplies.pvp')
                ->whereNull('supplies.deleted_at')
                ->get();
        
        return $this->renderHTML('/buys/suppliesList.html.twig', [
            'supplies' => $supplies
        ]);
    }
    
    public function getSuppliesDataAction($request)
    {
        $responseMessage = null;
        $supplie_temp = null;
        $mader = null;
        if($request->getMethod() == 'POST')
        {
            $postData = $request->getParsedBody();           
            $suppliesValidator = v::key('ref', v::stringType()->notEmpty())
                    ->key('mader_code', v::stringType())
                    ->key('name', v::stringType()->notEmpty());
            try{
                $suppliesValidator->assert($postData);
                $supplie = new Supplies();
                $supplie->id = $postData['id'];
                if($supplie->id)
                {
                    $supplie_temp = Supplies::find($supplie->id);
                    if($supplie_temp)
                    {
                        $supplie = $supplie_temp;
                    }
                }                
                $supplie->ref = $postData['ref'];
                $supplie->name = $postData['name'];
                $mader = Mader::where('name', '=', $postData['mader'])->first();
                $supplie->mader = $mader->id;
                $supplie->mader_code = $postData['mader_code'];
                $supplie->stock = $postData['stock'];
                $supplie->pvc = $this->tofloat($postData['pvc']);
                $supplie->pvp = $this->tofloat($postData['pvp']);
                $supplie->observations = $postData['observations'];                
                if($supplie_temp)
                {
                    $supplie->update();
                    $responseMessage = 'Updated';
                }
                else
                {
                    $supplie->save();
                    $responseMessage = 'Saved';
                }
                
            } catch (Exception $ex) {
                $responseMessage = $ex->getMessage();
            }
        }
        $selected_supply = null;
        $maders = Mader::All();
        $params = $request->getQueryParams();
        if($request->getQueryParams('id'))
        {            
            $selected_supply = DB::table('supplies')
                    ->join('maders', 'supplies.mader', '=', 'maders.id')
                    ->select('supplies.id', 'supplies.ref', 'supplies.name', 'maders.name as mader', 'supplies.pvc', 'supplies.pvp')
                    ->where('supplies.id', '=', $params['id'])
                    ->whereNull('supplies.deleted_at')
                    ->first();
        }    
        
        return $this->renderHTML('/buys/suppliesForm.html.twig', [
            'supply' => $selected_supply,
            'maders' => $maders,
            'responseMessage' => $responseMessage
        ]);
    }
    public function deleteAction(ServerRequest $request)
    {         
        $this->suppliesService->deleteSupplies($request->getQueryParams('id'));               
        return new RedirectResponse('/intranet/buys/supplies/list');
    }  
}
