<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Reports;

use FPDF;

/**
 * Description of SellOfferReport
 *
 * @author tonyl
 */
class SellOfferReport extends FPDF{
    
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
        $this->SetFont('Arial', 'B', 16);
        $this->SetFillColor(130,130,130);
        $this->SetTextColor(255, 255 ,255);
        $this->SetY(42);
        $this->Cell(0, 8, 'OFERTA', 1, 1, 'L', 1);
        $this->SetY(52);
        $this->SetFillColor(255);
        $this->SetTextColor(0);
        $this->Cell(0, 8, 'CLIENTE', 0, 1, 'L', 1);
        $this->SetY(60);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(30, 6, 'Nombre: ', 0,0,'L',1);              
        $this->SetFont('Arial','', 10);
        $this->Cell(80, 6, $data['name'], 0,0,'L', 1);
        $this->SetFont('Arial', 'B', 12);        
        $this->Cell(30, 6 , 'DNI/NIF: ', 0, 0,'L', 1);
        $this->SetFont('Arial','', 10);
        $this->Cell(50, 6, $data['fiscal_id'], 0, 1, 'L', 1);
        $this->SetFont('Arial', 'B', 12);  
        $this->Cell(30, 6, utf8_decode('Dirección: '), 0, 0, 'L', 1);
        $this->SetFont('Arial','', 10);
        $this->Cell(80, 6, $data['address'], 0, 0, 'L', 1);        
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(30, 6, 'C.P.: ', 0,0,'L',1);
        $this->SetFont('Arial','', 10);
        $this->Cell(30, 6 , $data['cp'], 0,1,'L', 1);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(30, 6, utf8_decode('Población: '), 0, 0, 'L', 1);
        $this->SetFont('Arial','', 10);
        $this->Cell(80, 6, utf8_decode($data['city']), 0, 0, 'L', 1); 
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(30, 6, 'Provincia: ', 0, 0, 'L', 1);
        $this->SetFont('Arial','', 10);
        $this->Cell(30, 6 , utf8_decode($data['state']), 0,1,'L', 1);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(30, 6, 'Pais: ', 0, 0, 'L', 1);
        $this->SetFont('Arial','', 10);
        $this->Cell(30, 6 , utf8_decode($data['country']), 0,1,'L', 1);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(30, 6, utf8_decode('Email: '), 0, 0, 'L', 1);
        $this->SetFont('Arial','', 10);
        $this->Cell(80, 6 , $data['email'], 0,0,'L', 1);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(30, 6, utf8_decode('Teléfono: '), 0, 0, 'L', 1);
        $this->SetFont('Arial','', 10);
        $this->Cell(30, 6 , utf8_decode($data['phone']), 0,1,'L', 1);
        $this->SetFillColor(130, 130, 130);
        $this->Cell(0,1,'', 0, 1, 'C', 1);
        $this->SetFillColor(255);
        $this->SetY(92);
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(30, 10, 'VEHICULO', 0, 1, 'L', 1);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(30, 6, utf8_decode('Matricula: '), 0, 0, 'L', 1);
        $this->SetFont('Arial','', 10);
        $this->Cell(50, 6 , utf8_decode($data['plate']), 0,0,'L', 1);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(30, 6, utf8_decode('Marca: '), 0, 0, 'L', 1);
        $this->SetFont('Arial','', 10);
        $this->Cell(30, 6 , utf8_decode($data['brand']), 0,0,'L', 1);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(30, 6, utf8_decode('Modelo: '), 0, 0, 'L', 1);
        $this->SetFont('Arial','', 10);
        $this->Cell(30, 6 , utf8_decode($data['model']), 0,1,'L', 1);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(30, 6, utf8_decode('Bastidor: '), 0, 0, 'L', 1);
        $this->SetFont('Arial','', 10);
        $this->Cell(50, 6 , utf8_decode($data['phone']), 0,1,'L', 1);
        
        
    }
    public function Footer()
    {
        
    }
    
}
