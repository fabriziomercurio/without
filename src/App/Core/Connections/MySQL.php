<?php
declare(strict_types=1); 

namespace App\Core\Connections; 
use App\Interfaces\Database; 
use App\Core\Env; 

class MySQL implements Database
{   
    public static function connect() : \PDO
    {   
        ENV::getContent();

        try {
            $conn = new \PDO('mysql:dbname='.ENV::$config['DATABASE'].';host='.ENV::$config['HOST'], ENV::$config['USER'],ENV::$config['PASSWORD']); 
            return $conn; 
        } catch (\Throwable $th) {
             throw new \Exception("Unable to establish a connection to the database " . $th->getMessage());
        }
    }  
}

?>