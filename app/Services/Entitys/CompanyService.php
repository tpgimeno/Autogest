<?php

namespace App\Services\Entitys;

use App\Models\Company;
use App\Services\BaseService;


class CompanyService extends BaseService {    
    public function searchCompanies($searchString){
        $companies = Company::Where("id", "like", "%".$searchString."%")
                ->orWhere("name", "like", "%".$searchString."%")
                ->orWhere("fiscalName", "like", "%".$searchString."%")
                ->orWhere("fiscalId", "like", "%".$searchString."%")
                ->orWhere("phone", "like", "%".$searchString."%")
                ->orWhere("email", "like", "%".$searchString."%")
                ->get(); 
        if(!$companies){
            $companies = $this->getAllRegisters(new Company());
        }
        return $companies;
    }
    public function findCompany($array){
        $company = null;
        if(isset($array['id']) && $array['id']){
            $company = Company::find(intval($array['id']));
        }
        return $company;
    }
    public function saveOrUpdate($array) {
        $company = new Company();          
        if($this->findCompany($array)) {
            $company = Company::find(intval($array['id']));            
        }
        return $company;
    }
    
    public function saveCompanyData($array) {
        $company = $this->saveOrUpdate($array);                            
        $company->name = $array['name'];
        $company->fiscalId = $array['fiscalId'];
        $company->fiscalName = $array['fiscalName'];
        $company->address = $array['address'];
        $company->city = $array['city'];
        $company->postalCode = $array['postalCode'];
        $company->state = $array['state'];
        $company->country = $array['country'];
        $company->phone = $array['phone'];
        $company->email = $array['email'];
        $company->site = $array['site'];
        if($this->findCompany($array)) {
            $company->update();
            $responseMessage = 'Updated';
        }else{
            $company->save();     
            $responseMessage = 'Saved'; 
        } 
        $response = ['id' => $company->id, 'responseMessage' => $responseMessage];
        return $response;
    }
    
    public function getModelProperties(){
        $company = new Company();
        
        return $company->getProperties();
    }
    
    public function deleteCompany($id) {        
        $company = Company::find($id)->first();
        $company->delete();
    }
}