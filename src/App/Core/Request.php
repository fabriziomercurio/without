<?php 
declare(strict_types=1); 

namespace App\Core; 

class Request 
{   
    private function getMethod() 
    {
        return $_SERVER['REQUEST_METHOD']; 
    } 

    public function getBody()
    {       
        if ($this->getMethod() === 'POST') {
            return $_POST; 
        } 
    }
} 


?>