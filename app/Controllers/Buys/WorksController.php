<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Controllers\Buys;

use App\Controllers\BaseController;
use App\Models\Works;
use App\Services\Buys\WorksService;
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
                ->select('works.id', 'works.reference', 'works.description', 'works.price', 'works.cantity')
                ->whereNull('works.deleted_at')
                ->get();
        
        return $this->renderHTML('/works/worksList.html.twig', [
            'works' => $works
        ]);
    }
    
    public function getWorkDataAction($request)
    {
        $responseMessage = null;
        $work_temp = null;        
        if($request->getMethod() == 'POST')
        {
            $postData = $request->getParsedBody();           
            $worksValidator = v::key('description', v::stringType()->notEmpty());
            try{
                $worksValidator->assert($postData);
                $work = new Works();
                $work->id = $postData['id'];
                if($work->id)
                {
                    $work_temp = Works::find($work->id);
                    if($work_temp)
                    {
                        $work = $work_temp;
                    }
                }                
                $work->reference = $postData['reference'];
                $work->description = $postData['description'];
                $work->price = $this->tofloat($postData['price']);
                $work->observations = $postData['observations'];                
                if($work_temp)
                {
                    $work->update();
                    $responseMessage = 'Updated';
                }
                else
                {
                    $work->save();
                    $responseMessage = 'Saved';
                }
                
            } catch (Exception $ex) {
                $responseMessage = $ex->getMessage();
            }
        }
        $selected_work = null;       
        $params = $request->getQueryParams();
        if($request->getQueryParams('id'))
        {            
            $selected_supply = DB::table('works')                   
                    ->select('works.id', 'works.reference', 'works.description', 'works.cantity', 'works.price')
                    ->where('works.id', '=', $params['id'])
                    ->whereNull('works.deleted_at')
                    ->first();
        }          
        return $this->renderHTML('/works/worksForm.html.twig', [
            'work' => $selected_work,            
            'responseMessage' => $responseMessage
        ]);
    }
    public function deleteAction(ServerRequest $request)
    {         
        $this->worksService->deleteWorks($request->getQueryParams('id'));               
        return new RedirectResponse('/intranet/vehicles/works/list');
    }  
}
