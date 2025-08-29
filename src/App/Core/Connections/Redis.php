<?php
declare(strict_types=1); 

namespace App\Core\Connections; 
use App\Interfaces\Database;

class Redis implements Database 
{
    public static function connect() 
    {
        echo 'connect with Redis ... '; 
    }  
}

?>