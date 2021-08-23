<?php

namespace App\Controllers\Vehicle;

use App\Controllers\BaseController;
use App\Models\Brand;
use App\Services\ErrorService;
use App\Services\Vehicle\BrandService;
use Illuminate\Database\QueryException;
use Laminas\Diactoros\ServerRequest;
use Respect\Validation\Validator as v;
use Laminas\Diactoros\Response\RedirectResponse;
use ZipStream\Exception;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of BrandController
 *
 * @author tonyl
 */
class BrandController extends BaseController
{
    protected $brandService;
    protected $errorService;
    
    public function __construct(BrandService $brandService, ErrorService $errorService) {
        parent::__construct();
        $this->brandService = $brandService;
        $this->errorService = $errorService;
    }
    public function getIndexAction(){
        $brands = Brand::All();
        return $this->renderHTML('/vehicles/brands/brandsList.html.twig', [
            'currentUser' => $this->currentUser->getCurrentUserEmailAction(),
            'brands' => $brands
        ]);
    }
    public function searchBrandAction($request)
    {
        $searchData = $request->getParsedBody();
        $searchString = $searchData['searchFilter'];        
        $brand = Brand::Where("name", "like", "%".$searchString."%")
                ->orWhere("id", "like", "%".$searchString."%")                
                ->get();     
        return $this->renderHTML('/vehicles/brands/brandsList.html.twig', [
            'currentUser' => $this->currenUser->getCurrentUserEmailAction(),
            'brands' => $brand                
        ]);
    }    
    public function getBrandDataAction($request)
    {                
        $responseMessage = null;
        if($request->getMethod() === 'POST')
        {
            $postData = $request->getParsedBody();            
            $brandValidator = v::key('name', v::stringType()->notEmpty());                       
            try{
                $brandValidator->assert($postData); // true                     
            }catch(Exception $e){                
                $responseMessage = $e->getMessage();
            }
            $brand = new Brand();
            if(isset($postData['id']) && $postData['id']){
                $brand = Brand::find($postData['id']);
            }
            $brand->name = $postData['name'];                          
            $responseMessage = $this->saveBrand($brand);
        }
        $brandSelected = $this->setBrand($request);        
        return $this->renderHTML('/vehicles/brands/brandsForm.html.twig', [
            'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
            'responseMessage' => $responseMessage,
            'brand' => $brandSelected
        ]);
    }
    public function saveBrand($brand){
        try{
            if(Brand::find($brand->id))
            {
                $brand->update();     
                $responseMessage = 'Updated';                
            }
            else
            {
                $brand->save();     
                $responseMessage = 'Saved';
                
            }
        } catch (QueryException $ex) {
            $responseMessage = $this->errorService->getError($ex);
            
        }
        return $responseMessage;
    }
    public function setBrand($request){
        $brandSelected = null;
        $params = $request->getQueryParams();
        if(isset($params['id']) && $params['id'])
        {
            $brandSelected = Brand::find($params['id']);
        }
        return $brandSelected;
    }
    public function deleteAction(ServerRequest $request)
    {         
        $this->brandService->deleteBrand($request->getQueryParams('id'));               
        return new RedirectResponse('/Intranet/vehicles/brands/list');
    }

}
