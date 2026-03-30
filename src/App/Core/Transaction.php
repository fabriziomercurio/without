<?php 
declare(strict_types=1);

namespace App\Core; 
use App\Core\Connections\Connection; 
use App\Core\Connections\MySQL; 

class Transaction
{
    private static $pdo; 

    public static function init() 
    {
       self::$pdo = Connection::connect(new MySql); 
    } 

    public static function beginTransaction() 
    {
        self::init(); 
        self::$pdo->beginTransaction(); 
    } 

    public static function commit() 
    {
        self::init();
        self::$pdo->commit(); 
    } 

     public static function rollback() 
    {
        self::init();

        if (self::$pdo->inTransaction()) {
            self::$pdo->rollback();
        }
         
    }

    
}