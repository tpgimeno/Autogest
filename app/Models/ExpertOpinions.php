<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Description of ExpertOpinions
 *
 * @author tonyl
 */
class ExpertOpinions extends Model{
    
    use SoftDeletes;
    protected $table = 'expertopinions';
    protected $properties = ['data' => ['opinionId', 
        'expertId', 
        'expertName', 
        'expertSurname',        
        'expertAddress',
        'expertState',
        'expertCP',
        'expertCity',
        'expertPhone',
        'expertEmail',
        'date',
        'demander'],
        'detail' => ['object',
        'actions',
        'conclusions',
        'anexed',
        'baseExpertOpinion',
        'tvaExpertOpinion',
        'totalExpertOpinion'],
        'vehicle' => ['vehicle_id',
        'brand',
        'model',
        'plate',
        'vin',
        'km'],                
        'components' => [],
        'supplies' => [],
        'works' => []];
    public function getProperties(){
        return $this->properties;
    }
}
