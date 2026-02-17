<?php
declare(strict_types=1); 

namespace App\Core\Connections; 
use App\Interfaces\Database; 

class Connection 
{   
    private static ?\PDO $pdo = null;

    public static function connect(Database $conn) : \PDO
    {           
        if (self::$pdo === null) {
            self::$pdo = $conn::connect(); 
        }
        return self::$pdo;
    }
}

?> 

 