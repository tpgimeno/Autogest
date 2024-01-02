<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Description of Assurances
 *
 * @author tonyl
 */
class Assurances extends Model{
    //put your code here
    protected $table = 'assurances';
    protected $properties = ['ref', 'inDate', 'effectDate','duration', 'getter_id','object_id','discount','price', 'observations', 'options'];
    public function getProperties(){
        return $this->properties;
    }
}
