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
use Illuminate\Support\Facades\Date;
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
//        var_dump($array);die();
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
        $properties = $model->getProperties();        
        $content = [];              
        $assetsNumber = 0;
        if(isset($properties['data']) && is_array($properties['data'])){
            $properties = $this->filterProperties($properties);            
                      
        }
        $response = $this->filterContent($array, $properties);
        $content = $response[0];  
        if ($this->findRegister($model, $array) == true) {
            $model = $model::find(intval($array['id']));
        }
        // At this point we receive the form inputs with 3 item more than object properties. So we do $i + 3 to correct it.
            
//            var_dump($content);
//            var_dump($properties);die();
        for ($i = 0; $i < (sizeof($content)); $i++) {            
            if ($properties[$i] == 'password') {
                $model->{$properties[$i]} = password_hash($content[$i], PASSWORD_DEFAULT);
            }elseif ($properties[$i] == 'plate' && is_int($content[$i])){                
                $model->vehicle_id = $content[$i];   
            }elseif (str_ends_with($properties[$i], 'Date')){   
                if ($properties[$i] === 'inDate' || $properties[$i] === 'outDate'){ 
                    $date = date_create_from_format('d/m/Y H:i:s', $content[$i]);                    
                    $model->{$properties[$i]} = $date;
                }else{
                    $model->{$properties[$i]} = date('Y/m/d', strtotime($content[$i]));
                }
            }elseif(strpos($content[$i], "€")){   
                $content[$i] = str_replace(".", "", $content[$i]);
                $model->{$properties[$i]} = floatval($content[$i]);
            }else{            
                $model->{$properties[$i]} = $content[$i];
            }
        }  
//        var_dump($model);die();
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
            $this->saveAssets($response[2], $model);
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
    
    public function filterProperties($properties){
        $keys = array_keys($properties);
        $total_properties = [];
        for($j = 0; $j < count($keys);$j++){
           foreach($properties[$keys[$j]] as $item){
               array_push($total_properties, $item);
           }               
        }        
        $properties = $total_properties;
        if($properties[0] === 'offerNumber'){
                $properties_array = array_slice($properties, 0, array_search('vehicle_id', $properties)+1);        
                $end_array = array_slice($properties, array_search('vehicle_id', $properties)+6, count($properties));
                $properties = array_merge($properties_array, $end_array);
            }
        if($properties[0] === 'orderNumber'){                
            $properties_array = array_slice($properties, 0, array_search('plate', $properties));
//                var_dump($properties_array);
            $end_array = array_slice($properties, array_search('plate', $properties)+3, count($properties));
//                var_dump($end_array);
            $properties = array_merge($properties_array, $end_array);
        }
        return $properties;              
    }
    
    public function filterContent($array, $properties){
        $content = [];
        $assets = null;
        for($i = 0; $i < count($properties); $i++){
            if($properties[$i] === 'vehicle_id'){
                array_push($content, $array['plate']);
            }
            if(isset($array[$properties[$i]])){
                array_push($content, $array[$properties[$i]]);
            }
        }
        return [$content, $assets];
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
