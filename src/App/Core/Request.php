<?php 
declare(strict_types=1); 

namespace App\Core; 

class Request 
{   
    private function getMethod() 
    {
        return $_SERVER['REQUEST_METHOD']; 
    } 


    /* file_get_contents("php://input"); 
    Legge il corpo grezzo della richiesta HTTP, 
    php://input è uno stream che ti permette di accedere ai dati inviati nel body, 
    utile quando il Content-Type è application/json. 
    */

    public function getBody() : array
    {       
      if (!empty($_POST)) { 
        return $_POST;
      }
      
      if ($this->getMethod() === 'POST' || $this->getMethod() === 'PUT'){  
              
        $raw = file_get_contents("php://input");
        return json_decode($raw, true) ?? [];
      }
        return []; 
    }
} 


?>