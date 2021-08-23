<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Reports\Vehicles;

use FPDF;

define('EURO', chr(128));

/**
 * Description of VehiclesReport
 *
 * @author Tony Pinto Gimeno
 */
class ComponentsReport extends FPDF
{    
    public function Header()
    {
        $this->Image('images/pngs/logo-correo-transparente.png', 10, 10, 80);  
        $this->SetFont('Arial', 'B', 12);      
        $this->Cell(0, 5, 'AUTOMOTIVE SERVICES 2012 SLU', 0, 0, 'R', null, false);
        $this->Cell(0, 15, 'B12927695', 0, 0, 'R', null, false);
        $this->Cell(0, 25, 'CRTRA. N-340a KM 1043,5', 0, 0, 'R', null, false);
        $this->Cell(0, 35, 'BENICARLO 12580 CASTELLON', 0, 0, 'R', null, false);
        $this->Cell(0, 45, 'TEL: 964471950', 0, 0, 'R', null, false);
        $this->Cell(0, 55, 'EMAIL: adm@automotiveservices.es', 0, 0, 'R',null, false); 
        $this->Ln(); 
        $this->SetFont('Arial', 'B', 12);
        $this->SetFillColor(130,130,130);
        $this->SetTextColor(255, 255 ,255);
        $this->SetY(42);
        $this->Cell(0, 8, 'COMPONENTES', 0, 1, 'L', 1);
        $this->SetY(50);
        $this->SetFillColor(255);
        $this->SetTextColor(0);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(20, 6, 'ID ', 0,0,'C',1);
        $this->Cell(35, 6, 'Referencia ', 0,0,'L',1); 
        $this->Cell(85 , 6, 'Nombre ', 0,0,'L',1);               
        $this->Cell(25, 6, 'Coste ', 0,0,'C',1);
        $this->Cell(25, 6, 'Pvp ', 0,1,'C',1);
    }     
    public function Body($data)
    {       
//        var_dump($data);die();
        $this->SetFillColor(255);
        $this->SetTextColor(0);
        for($i=0;$i<count($data['components']);$i++){                          
            $this->SetFont('Arial','', 8);
            $this->Cell(20, 6, $data['components'][$i]->id, 0,0,'C', 1);
            $this->Cell(35, 6, $data['components'][$i]->ref, 0,0,'L', 1); 
            $this->Cell(85, 6, $data['components'][$i]->name, 0,0,'L', 1);                              
            $this->Cell(25, 6, number_format($this->tofloat($data['components'][$i]->pvc),2,'.',',')." ".EURO, 0,0,'C', 1);
            $this->Cell(25, 6, number_format($this->tofloat($data['components'][$i]->pvp),2,'.',',')." ".EURO, 0,1,'C', 1);
            
        }       
    }
    public function Footer()
    {
        $this->SetY(275);
        $this->SetFont('Arial', 'B', 7);
        $this->SetTextColor(255,255,255);
        $this->SetFillColor(0);
        $this->Cell(0, 4, 'AUTOMOTIVE SERVICES 2014 SLU', 0, 1, 'C', true);        
        $this->Cell(0, 4, 'CRTRA. N-340a KM 1043,5 - BENICARLO 12580 CASTELLON', 0, 1, 'C', true);        
        $this->Cell(0, 4, 'TEL: 964471950 - EMAIL: adm@automotiveservices.es', 0, 1, 'C', true);
        
    }
    function tofloat($num) 
    {
        $dotPos = strrpos($num, ',');
        $commaPos = strrpos($num, '.');
        $sep = (($dotPos > $commaPos) && $dotPos) ? $dotPos :
        ((($commaPos > $dotPos) && $commaPos) ? $commaPos : false);        
        if (!$sep) 
        {
            $value = floatval(preg_replace("/[^0-9]/", "", $num));
        }
        else
        {
            $value = floatval(
                preg_replace("/[^0-9]/", "", substr($num, 0, $sep)) . '.' .
                preg_replace("/[^0-9]/", "", substr($num, $sep+1, strlen($num)))
            );  
        }       
        return $value;            
    }
}
