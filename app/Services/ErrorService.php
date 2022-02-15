<?php

/*

 */
namespace App\Services;
/**
 * Description of ErrorService:
 * This is a service to handle all errors from MySqlServer
 * 1062     "El valor duplicado ya existe en la base de datos"
 * 
 * 
 * 
 * @author tonyl
 */
class ErrorService {
    
    public function getError($ex)
    {
        while(isset($ex->errorInfo))
        {
            switch($ex->errorInfo[1])
            {
            case('1062'):
                return 'El valor ya existe en la base de datos';
                break;            
            default:
                return $ex->errorInfo[2]; 
                break;
            }
        }       
        return $ex->getMessages()['name'];
    }    
   
}
