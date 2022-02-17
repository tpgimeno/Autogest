<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Services;

use Exception;

/**
 * Description of BaseService
 *
 * @author tonyl
 */
class BaseService {
   public function getAllRegisters($model){
       $data = $model::all();
       return $data;      
   }
   public function setInstance($model, $array){        
        if(isset($array['id'])) {
            $instanceSelected = $model::find(intval($array['id']));
        }else{
            $instanceSelected = $model;
        }
        return $instanceSelected;  
   }
   public function findRegister($model, $array){       
        $exist = false;
        if(isset($array['id']) && $model::find(intval($array['id']))){
            $exist = true;
        }
        return $exist;
   }
   public function saveRegister($model, $array){      
       $properties = $model->getProperties();
       $content = array_values($array);   
       if($this->findRegister($model, $array) == true){
           $model = $model::find(intval($array['id']));
       }
       
       for ($i = 0; $i<sizeof($properties); $i++){
           $model->{$properties[$i]} = $content[$i+1];
       }       
       
       try{
            if($this->findRegister($model, $array) == true){                
                $model->update();
                $responseMessage = 'Updated';
            }else{
                $model->save();
                $responseMessage = 'Saved';
            }
       }catch(Exception $e){
           $responseMessage = $e->getMessage();
       }
       return $responseMessage;      
   }
   public function deleteRegister($model, $id){         
       $model::find(intval($id['id']))->delete();       
   }
}
