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
use App\Services\Vehicle\SuppliesService;
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
                ->select('supplies.id', 'supplies.ref', 'maders.name as mader', 'supplies.name', 'supplies.stock', 'supplies.maderCode', 'supplies.pvc', 'supplies.pvp')
                ->whereNull('supplies.deleted_at')
                ->get();
        
        return $this->renderHTML('/vehicles/supplies/suppliesList.html.twig', [
            'supplies' => $supplies
        ]);
    }
    
    public function getSuppliesDataAction($request)
    {
        $responseMessage = null;
        $supply_temp = null;
        $mader = null;
        if($request->getMethod() == 'POST')
        {
            $postData = $request->getParsedBody();           
            $suppliesValidator = v::key('ref', v::stringType()->notEmpty())
                    ->key('mader_code', v::stringType())
                    ->key('name', v::stringType()->notEmpty());
            try{
                $suppliesValidator->assert($postData);
                $supply = new Supplies();
                $supply->id = $postData['id'];
                if($supply->id)
                {
                    $supply_temp = Supplies::find($supply->id);
                    if($supply_temp)
                    {
                        $supply = $supply_temp;
                    }
                }                
                $supply->ref = $postData['ref'];
                $supply->name = $postData['name'];
                $mader = Mader::where('name', '=', $postData['mader'])->first();
                $supply->mader = $mader->id;
                $supply->mader_code = $postData['mader_code'];
                $supply->stock = $postData['stock'];
                $supply->pvc = $this->tofloat($postData['pvc']);
                $supply->pvp = $this->tofloat($postData['pvp']);
                $supply->observations = $postData['observations'];                
                if($supply_temp)
                {
                    $supply->update();
                    $responseMessage = 'Updated';
                }
                else
                {
                    $supply->save();
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
        
        return $this->renderHTML('/vehicles/supplies/suppliesForm.html.twig', [
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
