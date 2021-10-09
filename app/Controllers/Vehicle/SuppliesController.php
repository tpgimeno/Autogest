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
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\QueryException;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Diactoros\ServerRequest;
use Respect\Validation\Validator as v;
use ZipStream\Exception;



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
        if($request->getMethod() == 'POST')
        {
            $postData = $request->getParsedBody();
            if($this->validateData($postData))
            {
                $responseMessage = $this->validateData($postData);
            }
            $supply = $this->getSupplyData($postData);
            $responseMessage = $this->saveSupply($supply);           
            
        }        
        $maders = Mader::All();
        $params = $request->getQueryParams();
        $selected_supply = $this->setSupplyData($params);      
        return $this->renderHTML('/vehicles/supplies/suppliesForm.html.twig', [
            'supply' => $selected_supply,
            'maders' => $maders,
            'responseMessage' => $responseMessage
        ]);
    }
    public function validateData($postData)
    {
        $responseMessage = null;
        $suppliesValidator = v::key('ref', v::stringType()->notEmpty())
                    ->key('mader_code', v::stringType())
                    ->key('name', v::stringType()->notEmpty());
        try{
            $suppliesValidator->assert($postData);               
        } catch (Exception $ex) {
            $responseMessage = $ex->getMessage();
        }
        return $responseMessage;
    }
    public function setSupplyData($params)
    {
        $selected_supply = null;        
        if(isset($params['id']) && $params['id'])
        {            
            $selected_supply = DB::table('supplies')
                    ->join('maders', 'supplies.mader', '=', 'maders.id')
                    ->select('supplies.id', 'supplies.ref', 'supplies.name', 'maders.name as mader', 'supplies.pvc', 'supplies.pvp')
                    ->where('supplies.id', '=', $params['id'])
                    ->whereNull('supplies.deleted_at')
                    ->first();
        }
        return $selected_supply;
    }
    public function getSupplyData($postData)
    {
        $supply = new Supplies();        
        if(Supplies::find($postData['id']))
        {
            $supply = Supplies::find(intval($postData['id']));                   
        }                
        $supply->ref = $postData['ref'];
        $supply->name = $postData['name'];
        $mader = Mader::where('name', 'like', "%".$postData['mader']."%")->first();        
        $supply->mader = $mader->id;
        $supply->maderCode = $postData['mader_code'];
        $supply->stock = intval($postData['stock']);
        $supply->pvc = $this->tofloat($postData['pvc']);
        $supply->pvp = $this->tofloat($postData['pvp']);
        $supply->observations = $postData['observations'];        
        return $supply;
    }
    public function saveSupply($supply)
    {
        try{
            if(Supplies::find($supply->id))
            {
                $supply->update();
                $responseMessage = 'Updated';
            }
            else
            {
                $supply->save();
                $responseMessage = 'Saved';
            }
        } catch (QueryException $ex) {
            $responseMessage = $this->errorService->getError($ex);
        }       
        return $responseMessage;
    }
    public function deleteAction(ServerRequest $request)
    {         
        $this->suppliesService->deleteSupplies($request->getQueryParams('id'));               
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
