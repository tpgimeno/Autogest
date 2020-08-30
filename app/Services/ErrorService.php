<?php

/*

 */
namespace App\Services;
/**
 * Description of ErrorService:
 * This is a service to handle all errors from MySqlServer
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
                return 'El valor está duplicado';
                break;
            default:
                return $info[2]; 
                break;
            }
        }
       
        return $ex->getMessages()['name'];
    }    
   
}
