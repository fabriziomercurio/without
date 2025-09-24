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

    public static function success(string $message, object $data, $statusCode = 200) : void 
    {       
       self::json(array_merge(['success' => true, 'message' => $message, 'data' => $data, 'statusCode' => $statusCode]));      
    } 

    public static function error(string $message, object $data, $statusCode = 400) : void 
    {       
       self::json(array_merge(['error' => false, 'message' => $message, 'data' => $data, 'statusCode' => $statusCode]));      
    }
}