<?php 
declare(strict_types=1); 

namespace App\Core; 

class Request 
{   
    public array $extra = []; 

    private string $method; 
    
    public function __construct() 
    {
      $this->method = $_POST['_method'] ?? $_SERVER['REQUEST_METHOD'];
    } 

    public function getMethod() 
    {
        return strtoupper($this->method); 
    } 


    /* file_get_contents("php://input"); 
    Legge il corpo grezzo della richiesta HTTP, 
    php://input è uno stream che ti permette di accedere ai dati inviati nel body, 
    utile quando il Content-Type è application/json. 
    */ 

    public function getBody(): array
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

        // 'application/json'
        if (str_contains($contentType, 'application/json')) {
            $raw = file_get_contents("php://input");
            return json_decode($raw, true) ?? [];
        }

        // multipart/form-data
        if (str_contains($contentType, 'multipart/form-data')) {         
            return $_POST;
        }

        // application/x-www-form-urlencoded
        return $_POST;
    }
} 


?>