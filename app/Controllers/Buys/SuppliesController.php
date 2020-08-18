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
use Respect\Validation\Validator as v;
use Symfony\Component\Config\Definition\Exception\Exception;

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
        $supplies = Supplies::All();
        return $this->renderHTML('/buys/suppliesList.html.twig', [
            'supplies' => $supplies
        ]);
    }
    
    public function getSuppliesDataAction($request)
    {
        $responseMessage = null;
         $supplie_temp = null;
        if($request->getMethod() == 'POST')
        {
            $postData = $request->getParsedBody();
           
            $suppliesValidator = v::key('ref', v::stringType()->notEmpty())
                    ->key('mader_code', v::stringType()->notEmpty())
                    ->key('name', v::stringType()->notEmpty());
            try{
                $suppliesValidator->assert($postData);
                $supplie = new Supplies();
                $supplie->id = $postData['id'];
                if($supplie->id)
                {
                    $supplie_temp = Supplies::find($supplie->id)->first();
                    if($supplie_temp)
                    {
                        $supplie = $supplie_temp;
                    }
                }
                $supplie->ref = $postData['ref'];
                $supplie->name = $postData['name'];
                $supplie->mader = $postData['mader'];
                $supplie->mader_code = $postData['mader_code'];
                $supplie->stock = $postData['stock'];
                $supplie->pvc = $postData['pvc'];
                $supplie->pvp = $postData['pvp'];
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
        $selected_supplie = null;
        $maders = Mader::All();
        if($request->getQueryParams('id'))
        {
            $selected_supplie = Supplies::find($request->getQueryParams('id'))->first();
        }
        return $this->renderHTML('/buys/suppliesForm.html.twig', [
            'supplie' => $selected_supplie,
            'maders' => $maders,
            'responseMessage' => $responseMessage
        ]);
    }
}
