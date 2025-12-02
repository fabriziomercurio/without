<?php 

namespace App\Core; 

class Response 
{
    public static function json(array $data) : void
    {    
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    } 

    public static function success(string $message, mixed $data = null, $statusCode = 200, string $token = '', string $csrf = '') : void 
    {       
       self::json(array_merge(['success' => true, 'message' => $message, 'data' => $data, 
       'statusCode' => $statusCode, 'token' => $token, 'csrf' => $csrf]));      
    } 

    public static function error(string $message, mixed $data = null, $statusCode = 400) : void 
    {       
       self::json(array_merge(['error' => false, 'messageError' => $message, 'data' => $data, 'statusCode' => $statusCode]));      
    }
}