<?php
declare(strict_types=1); 

namespace App\Core\Connections; 
use App\Interfaces\Database; 

class MySQL implements Database
{
    public static function connect() 
    {
        try {
            $conn = new \PDO('mysql:dbname=mvc;host='.'db', 'root', 'root'); 
            return $conn; 
        } catch (\Throwable $th) {
             die("Impossibile stabilire una connessione al db " . $th->getMessage());
        }
    }  
}

?>