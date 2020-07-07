<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Controllers;

use App\Reports\PruebaReport;

/**
 * Description of PruebaReportController
 *
 * @author tonyl
 */
class PruebaReportController extends BaseController 
{
    public function getReportAction()
    {
        $report = new PruebaReport();
        $report->getPdfPrueba();
    }
    
}
