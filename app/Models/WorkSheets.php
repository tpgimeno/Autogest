<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Description of WorkSheets
 *
 * @author tonyl
 */
class WorkSheets extends Model{
    
    use SoftDeletes;

    protected $table = "worksheets";
    
    protected $properties = ['data' => ['workSheetNumber',
        'order_id',
        'customer_id',
        'vehicle_id',
        'plate',
        'brand',
        'model', 
        'inDate',
        'outDate',
        'workId'],
        'detail' => ['baseWorkSheet',
        'discountWorkSheet',
        'tvaWorkSheet',
        'totalWorkSheet',
        'text',
        'observations'],
        'components' => [],
        'supplies' => [],
        'works' => []];
    
    public function getProperties(){
        return $this->properties;
    }
}
