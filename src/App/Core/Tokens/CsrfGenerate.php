<?php 
declare(strict_types=1); 

namespace App\Core\tokens; 
use App\Interfaces\Csrf; 

class CsrfGenerate implements Csrf  
{
    public static function generate() : string 
    {
       return bin2hex(random_bytes(16));
    }
}


?>