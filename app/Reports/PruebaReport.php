<?php


namespace App\Reports;

use FPDF;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once "../vendor/autoload.php";

/**
 * Description of PruebaReport
 *
 * @author tonyl
 */
class PruebaReport extends FPDF
{
    public function getPdfPrueba()
    {
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(40, 10, 'Hola Mundo');
        $pdf->Output();
    }
    
}
