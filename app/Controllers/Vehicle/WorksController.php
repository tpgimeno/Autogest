<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Controllers\Vehicle;

use App\Controllers\BaseController;
use App\Models\Works;
use App\Services\Vehicle\WorksService;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Diactoros\ServerRequest;
use ZipStream\Exception;
use Respect\Validation\Validator as v;
use Illuminate\Database\Capsule\Manager as DB;



/**
 * Description of WorksController
 *
 * @author tonyl
 */
class WorksController extends BaseController
{
    protected $worksService;    
    public function __construct(WorksService $worksService) {
        parent::__construct();
        $this->worksService = $worksService;
    }
    public function getIndexAction()
    {
        $works = DB::table('works')                
                ->select('works.id', 'works.reference', 'works.description', 'works.pvp')
                ->whereNull('works.deleted_at')
                ->get();        
        return $this->renderHTML('/vehicles/works/worksList.html.twig', [
            'works' => $works
        ]);
    }    
    public function getWorkDataAction($request)
    {
        $responseMessage = null;             
        if($request->getMethod() == 'POST')
        {
           $postData = $request->getParsedBody();           
           if($this->validateData($postData))
           {
               $responseMessage = $this->validateData($postData);
           }
           $work = $this->addWorkData($postData);
           $responseMessage = $this->saveWork($work);           
        }
            
        $params = $request->getQueryParams();
        $selectedWork = $this->setWork($params);                  
        return $this->renderHTML('/vehicles/works/worksForm.html.twig', [
            'work' => $selectedWork,            
            'responseMessage' => $responseMessage
        ]);
    }
    public function validateData($postData)
    {
        $responseMessage = null;
        $worksValidator = v::key('description', v::stringType()->notEmpty());
        try{
            $worksValidator->assert($postData);                
        } catch (Exception $ex) {
            $responseMessage = $ex->getMessage();
        }
        return $responseMessage;
    }
    public function setWork($params)
    {
        $selectedWork = null;
        if(isset($params['id']) && $params['id'])
        {            
            $selectedWork = DB::table('works')                   
                    ->select('works.id', 'works.reference', 'works.description', 'works.pvp')
                    ->where('works.id', '=', $params['id'])
                    ->whereNull('works.deleted_at')
                    ->first();
        }
        return $selectedWork;
    }
    public function addWorkData($postData)
    {
        $work = new Works();                
        if(isset($postData['id']) && $postData['id'])
        {
            $work = Works::find(intval($postData['id']));                                
        }                
        $work->reference = $postData['reference'];
        $work->description = $postData['description'];
        $work->pvp = $this->tofloat($postData['price']);
        $work->observations = $postData['observations']; 
        return $work;
    }
    public function saveWork($work)
    {
        $responseMessage = null;
        try{
            if(Works::find($work->id))
            {
                $work->update();
                $responseMessage = 'Updated';
            }
            else
            {
                $work->save();
                $responseMessage = 'Saved';
            }
        } catch (QueryException $ex) {
            $responseMessage = $this->errorService->getError($ex);
        }
        return $responseMessage;
    }
    public function deleteAction(ServerRequest $request)
    {         
        $this->worksService->deleteWorks($request->getQueryParams('id'));               
        return new RedirectResponse('/Intranet/vehicles/works/list');
    }  
}
