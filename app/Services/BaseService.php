<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Services;

use App\Models\Label;
use Exception;

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
        $object = new $model;        
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
        $properties = $model->getProperties();
        $content = array_values($array);
//       var_dump($properties);
//       var_dump($content);die();
        unset($content[0], $content[1]);
        if ($this->findRegister($model, $array) == true) {
            $model = $model::find(intval($array['id']));
        }

        // At this point we receive the form inputs with 3 item more than object properties. So we do $i + 3 to correct it.

        for ($i = 0; $i < sizeof($properties); $i++) {
            if ($properties[$i] == 'password') {
                $model->{$properties[$i]} = password_hash($content[$i + 3], PASSWORD_DEFAULT);
            }
            $model->{$properties[$i]} = $content[$i + 3];
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
        return array($model->id, $responseMessage);
    }

    public function deleteRegister($model, $array) {
        $model::find(intval($array['id']))->delete();
    }

}
