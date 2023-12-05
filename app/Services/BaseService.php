<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Services;

use App\Models\Accesories;
use App\Models\Label;
use App\Models\VehicleAccesories;
use Exception;
use function str_ends_with;

/**
 * Description of BaseService
 *
 * @author tonyl
 */
class BaseService {

    public function getAllRegisters($model) {
        $data = $model::all();
        return $data;
    }

    public function getLabelsArray() {
        $labels = Label::get()->all();
        $arrayLabels = null;
        for ($i = 0; $i < sizeof($labels); $i++) {
            $arrayLabels[$labels[$i]->property] = $labels[$i]->label;            
        }
        return $arrayLabels;
    }
    
    public function getModelProperties($model){                
        return $model->getProperties();
    }

    public function setInstance($model, $array) {
        if (isset($array['id'])) {
            $instanceSelected = $model::find(intval($array['id']));
        } else {
            $instanceSelected = $model;
        }
        return $instanceSelected;
    }

    public function findRegister($model, $array) {
        $exist = false;
        if (isset($array['id']) && $model::find(intval($array['id']))) {
            $exist = true;
        }
        return $exist;
    }

    public function saveRegister($model, $array) {
        var_dump($model);
        var_dump($array);die();
        $properties = $model->getProperties();
        $content = array_values($array);
        $diference = 3;
        $keys = [];
        $assetsNumber = 0;
        if(isset($properties['data']) && is_array($properties['data'])){
            $keys = array_keys($properties);
            $total_properties = [];
            for($j = 0; $j < count($keys);$j++){            
               foreach($properties[$keys[$j]] as $item){
                   array_push($total_properties, $item);
               }                  
            }
            $properties = $total_properties;
            $properties_array = array_slice($properties, 0, array_search('vehicle_id', $properties)+1);        
            $end_array = array_slice($properties, array_search('vehicle_id', $properties)+6, count($properties));
            $properties = array_merge($properties_array, $end_array);
        }        
        unset($content[0], $content[1],$content[2]);
        if(count($content) > count($properties)){
            $assetsNumber = count($content) - count($properties); 
            $assets = array_slice($content, count($content) - $assetsNumber);
            $content = array_slice($content, 0, count($content) - $assetsNumber);
            $diference = 0;
        }
        if ($this->findRegister($model, $array) == true) {
            $model = $model::find(intval($array['id']));
        }
        // At this point we receive the form inputs with 3 item more than object properties. So we do $i + 3 to correct it.
//            var_dump($properties);
//            var_dump($array);
        for ($i = 0; $i < (sizeof($properties)); $i++) {
            if ($properties[$i] == 'password') {
                $model->{$properties[$i]} = password_hash($content[$i+$diference], PASSWORD_DEFAULT);
            }elseif (str_ends_with($properties[$i], 'Date')){
                $model->{$properties[$i]} = date('Y/m/d', strtotime($content[$i+$diference]));
            }elseif(in_array("€", str_split($properties[$i]))){                
                $model->{$properties[$i]} = floatval($content[$i+$diference]);
            }else{            
                $model->{$properties[$i]} = $content[$i+$diference];
            }
        } 
        
        try {
            if ($this->findRegister($model, $array) == true) {
                $model->update();
                $responseMessage = 'Updated';
            } else {
                $model->save();
                $responseMessage = 'Saved';
            }
        } catch (Exception $e) {
            $responseMessage = $e->getMessage();
        }
        if($assetsNumber > 0){  
            $this->saveAssets($assets, $model);
        }
        return array($model->id, $responseMessage);
    }
    
    public function saveAssets($assets, $model){        
        $accesory = new VehicleAccesories();        
        $accesories = $this->getAllRegisters(new Accesories())->toArray(); 
        $array_keystrings = [];
        for($i=0;$i<count($assets);$i++){    
            for($j=0;$j<count($accesories);$j++){                
                if($assets[$i] === $accesories[$j]['keyString']){
                    $accesory->vehicle_id = $model->id;                    
                    $accesory->accesory_id = $accesories[$j]['id']; 
                    $vehicle_accesory = VehicleAccesories::where('vehicle_id', '=', $model->id)
                            ->where('accesory_id', '=', $accesories[$j]['id'])
                            ->get()->first();                   
                    if($vehicle_accesory){
                        $vehicle_accesory->update();
                    }else{
                        $accesory->save();
//                        var_dump($accesory);
                    }                    
                }                
            }          
        }        
        if(count($accesories) > count($assets)){
            for($j=0;$j<count($accesories);$j++){   
                array_push($array_keystrings, $accesories[$j]['keyString']);                
            }
            
            $unselected_accesories = array_diff($array_keystrings, $assets);
            for($i = 0;$i < count($unselected_accesories);$i++){
                $accesory_temp = Accesories::where('keyString', '=', $unselected_accesories[$i])->get()->first();
                $vehicle_accesory_unchecked = VehicleAccesories::where('vehicle_id', '=', $model->id)
                        ->where('accesory_id', '=', $accesory_temp->id);
                if($vehicle_accesory_unchecked){
                    $vehicle_accesory_unchecked->delete();
                }
            }
        }
        
        
    }

    public function deleteRegister($model, $array) {
        $model::find(intval($array['id']))->delete();
    }
    
    public function cleanString($string){
        $string = trim($string);

        $string = str_replace(
                array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'), array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'), $string
        );

        $string = str_replace(
                array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'), array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'), $string
        );

        $string = str_replace(
                array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'), array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'), $string
        );

        $string = str_replace(
                array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'), array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'), $string
        );

        $string = str_replace(
                array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'), array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'), $string
        );

        $string = str_replace(
                array('ñ', 'Ñ', 'ç', 'Ç'), array('n', 'N', 'c', 'C',), $string
        );

        $string = preg_replace("#[^A-Za-z0-9 ]#", "", $string);

        return $string;
    }

}
