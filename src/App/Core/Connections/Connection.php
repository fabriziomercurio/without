<?php
declare(strict_types=1); 

namespace App\Core\Connections; 
use App\Interfaces\Database; 

class Connection 
{   
    public static function connect(Database $conn) 
    {   
        return $conn::connect();
    }
}

?> 

 