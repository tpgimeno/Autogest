<?php

namespace App\Controllers\Entitys;

use App\Controllers\BaseController;
use App\Models\Brand;
use Laminas\Diactoros\ServerRequest;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Respect\Validation\Validator as v;

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
    public function getIndexAction()
    {
        $brands = Brand::All();
        return $this->renderHTML('/brands/brandsList.html.twig', [
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
        return $this->renderHTML('/brands/brandsList.html.twig', [
            'currentUser' => $this->currenUser->getCurrentUserEmailAction(),
            'brands' => $brand
                
        ]);
    }
    
    public function getBrandDataAction($request)
    {                
        $responseMessage = null;
        if($request->getMethod() == 'POST')
        {
            $postData = $request->getParsedBody();
            
            $brandValidator = v::key('name', v::stringType()->notEmpty());                       
            try{
                $brandValidator->assert($postData); // true 
                $brand = new Brand();
                $brand->name = $postData['name'];                          
                $brand->save();     
                $responseMessage = 'Saved';     
            }catch(Exception $e){                
                $responseMessage = $e->getMessage();
            }              
        }
        $brandSelected = null;
        if($_GET)
        {
            $brandSelected = Brand::find($_GET['id']);
        }
        return $this->renderHTML('/brands/brandsForm.html.twig', [
            'userEmail' => $this->currentUser->getCurrentUserEmailAction(),
            'responseMessage' => $responseMessage,
            'brand' => $brandSelected
        ]);
    }

    public function deleteAction(ServerRequest $request)
    {
         
        $this->brandService->deleteBrand($request->getQueryParams('id'));               
        return new RedirectResponse('/intranet/brands/list');
    }

}
