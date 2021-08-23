<?php

namespace App\Services\Entitys;

use App\Models\Company;


class CompanyService
{
    public function deleteCompany($id)
    {        
        $company = Company::find($id)->first();
        $company->delete();
    }
}