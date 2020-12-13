<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Reports\Sells;

use FPDF;
use const EURO;
use function utf8_decode;

define('EURO', chr(128));

/**
 * Description of SellOfferReport
 *
 * @author tonyl
 */
class SellOfferVehicleReport extends FPDF
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
    }     
    public function Body($data)
    {       
//        var_dump($data);die();
        $this->SetFont('Arial', 'B', 12);
        $this->SetFillColor(130,130,130);
        $this->SetTextColor(255, 255 ,255);
        $this->SetY(42);
        $this->Cell(0, 8, 'OFERTA', 0, 1, 'L', 1);
        $this->SetY(52);
        $this->SetFillColor(255);
        $this->SetTextColor(0);
        $this->Cell(0, 8, 'CLIENTE', 0, 1, 'L', 1);
        $this->SetY(60);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(30, 6, 'Nombre: ', 0,0,'L',1);              
        $this->SetFont('Arial','', 9);
        $this->Cell(80, 6, $data['postData']['name'], 0,0,'L', 1);
        $this->SetFont('Arial', 'B', 10);        
        $this->Cell(30, 6 , 'DNI/NIF: ', 0, 0,'L', 1);
        $this->SetFont('Arial','', 9);
        $this->Cell(50, 6, $data['postData']['fiscal_id'], 0, 1, 'L', 1);
        $this->SetFont('Arial', 'B', 10);  
        $this->Cell(30, 6, utf8_decode('Dirección: '), 0, 0, 'L', 1);
        $this->SetFont('Arial','', 9);
        $this->Cell(80, 6, $data['postData']['address'], 0, 0, 'L', 1);        
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(30, 6, 'C.P.: ', 0,0,'L',1);
        $this->SetFont('Arial','', 9);
        $this->Cell(30, 6 , $data['postData']['cp'], 0,1,'L', 1);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(30, 6, utf8_decode('Población: '), 0, 0, 'L', 1);
        $this->SetFont('Arial','', 9);
        $this->Cell(80, 6, utf8_decode($data['postData']['city']), 0, 0, 'L', 1); 
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(30, 6, 'Provincia: ', 0, 0, 'L', 1);
        $this->SetFont('Arial','', 9);
        $this->Cell(30, 6 , utf8_decode($data['postData']['state']), 0,1,'L', 1);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(30, 6, 'Pais: ', 0, 0, 'L', 1);
        $this->SetFont('Arial','', 9);
        $this->Cell(30, 6 , utf8_decode($data['postData']['country']), 0,1,'L', 1);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(30, 6, utf8_decode('Email: '), 0, 0, 'L', 1);
        $this->SetFont('Arial','', 9);
        $this->Cell(80, 6 , $data['postData']['email'], 0,0,'L', 1);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(30, 6, utf8_decode('Teléfono: '), 0, 0, 'L', 1);
        $this->SetFont('Arial','', 9);
        $this->Cell(30, 6 , utf8_decode($data['postData']['phone']), 0,1,'L', 1);        
        $this->SetFillColor(130, 130, 130);
        $this->Cell(0,1,'', 0, 1, 'C', 1);
        $this->SetFillColor(255);        
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(30, 10, 'VEHICULO', 0, 1, 'L', 1);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(30, 6, utf8_decode('Matricula: '), 0, 0, 'L', 1);
        $this->SetFont('Arial','', 9);
        $this->Cell(30, 6 , utf8_decode($data['postData']['plate']), 0,0,'L', 1);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(30, 6, utf8_decode('Marca: '), 0, 0, 'L', 1);
        $this->SetFont('Arial','', 9);
        $this->Cell(30, 6 , utf8_decode($data['postData']['brand']), 0,0,'L', 1);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(30, 6, utf8_decode('Modelo: '), 0, 0, 'L', 1);
        $this->SetFont('Arial','', 9);
        $this->Cell(30, 6 , utf8_decode($data['postData']['model']), 0,1,'L', 1);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(30, 6, utf8_decode('Bastidor: '), 0, 0, 'L', 1);
        $this->SetFont('Arial','', 9);        
        $this->Cell(30, 6 , utf8_decode($data['postData']['vin']), 0,0,'L', 1);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(30, 6, utf8_decode('Descripción: '), 0, 0, 'L', 1);
        $this->SetFont('Arial','', 9);        
        $this->Cell(0, 6 , utf8_decode($data['postData']['description']), 0,1,'L', 1);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(30, 6, utf8_decode('Potencia: '), 0, 0, 'L', 1);
        $this->SetFont('Arial','', 9);        
        $this->Cell(30, 6 , utf8_decode($data['postData']['power']), 0,0,'L', 1);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(30, 6, utf8_decode('Color: '), 0, 0, 'L', 1);
        $this->SetFont('Arial','', 9);        
        $this->Cell(30, 6 , utf8_decode($data['postData']['color']), 0,0,'L', 1);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(30, 6, utf8_decode('Matriculación: '), 0, 0, 'L', 1);
        $this->SetFont('Arial','', 9);        
        $this->Cell(30, 6 , utf8_decode($data['postData']['registryDate']), 0,1,'L', 1);
        $this->Ln();
        $this->SetFillColor(130, 130, 130);
        $this->Cell(0,1,'', 0, 1, 'C', 1);
        $this->SetFillColor(255); 
        $this->SetFont('Arial', 'B', 10);        
        $this->Cell(30, 6, utf8_decode('Equipamiento: '), 0, 1, 'L', 1);
        
        $accesories = $data['selected_accesories'];
        for($i = 0; $i < count($accesories);$i++)
        {
            $this->SetFont('Arial','', 9);
            $this->Cell(30, 6 , utf8_decode($accesories[$i]->name), 0,0,'L', 1);
        }
        $this->Ln(8);
        $this->MultiCell(0, 6, utf8_decode($data['postData']['equipment']), 0, 1, 'L', 1);
        $this->Ln('20');
        $this->SetFillColor(130, 130, 130);
        $this->Cell(0,1,'', 0, 1, 'C', 1);
        $this->SetFillColor(255);
        $this->SetFont('Arial', 'B', 10); 
        $this->Cell(0,2,'', 0, 1, 'C', 1);
        $this->Cell(30, 6, utf8_decode('Comentarios: '), 0, 1, 'L', 1);
        $this->SetFont('Arial','', 9);
        $this->MultiCell(0, 6, utf8_decode($data['postData']['vehicle_comments']), 0, 1, 'L', 1);
        $this->Ln();
        $this->SetFillColor(130, 130, 130);
        $this->Cell(0,1,'', 0, 1, 'C', 1);
        $this->SetFillColor(255);
        $this->Cell(0,2,'', 0, 1, 'C', 1);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(40, 6, utf8_decode('PVP: '), 0, 0, 'L', 1);
        $this->SetFont('Arial','', 9);  
//        var_dump($data['postData']['price_vehicle']);die();
        $this->Cell(30, 6 , number_format($this->tofloat($data['postData']['price_vehicle']),2,',','.')." ".EURO, 0,0,'C', 1);        
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(20, 6, utf8_decode('IVA 21%: '), 0, 0, 'L', 1);
        $this->SetFont('Arial','', 9);        
        $this->Cell(40, 6 , number_format($this->tofloat($data['postData']['vehicle_tva']),2,'.',',')." ".EURO, 0,0,'C', 1);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(20, 6, utf8_decode('Total: '), 0, 0, 'L', 1);
        $this->SetFont('Arial','', 9);        
        $this->Cell(30, 6 , number_format($this->tofloat($data['postData']['vehicle_total']),2,'.',',')." ".EURO, 0,1,'R', 1);
        
        $this->SetFillColor(130, 130, 130);
        $this->Cell(0,1,'', 0, 1, 'C', 1);      
        $this->SetFillColor(255); 
        $this->Cell(0,1,'', 0, 1, 'C', 1);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(30, 6, utf8_decode('Observaciones: '), 0, 1, 'L', 1);
        $this->SetFont('Arial','', 9);        
        $this->MultiCell(0, 6, utf8_decode($data['postData']['observations']), 0,1,'L', 1);
        $this->Cell(0,1,'', 0, 1, 'C', 1);
        $this->SetFillColor(130, 130, 130);
        $this->Cell(0,1,'', 0, 1, 'C', 1);
        $this->SetFillColor(255); 
        $this->Cell(0,1,'', 0, 1, 'C', 1);
         $this->SetFont('Arial', 'B', 12);
        $this->Cell(30, 10, utf8_decode('Base Oferta: '), 0, 0, 'L', 1);
        $this->SetFont('Arial','', 10);        
        $this->Cell(30, 10 , number_format($this->tofloat($data['postData']['price']),2,',','.')." ".EURO, 0,0,'C', 1);        
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(20, 10, utf8_decode('IVA 21%: '), 0, 0, 'C', 1);
        $this->SetFont('Arial','', 10);        
        $this->Cell(40, 10 , number_format($this->tofloat($data['postData']['tva']),2,'.',',')." ".EURO, 0,0,'C', 1);
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(30, 10, utf8_decode('Total Oferta: '), 0, 0, 'L', 1);
        $this->SetFont('Arial','', 12);        
        $this->Cell(30, 10 , number_format($this->tofloat($data['postData']['total']),2,'.',',')." ".EURO, 0,1,'R', 1);
        
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
