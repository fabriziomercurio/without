<?php 

require_once "autoload.php"; 

use App\Core\Connections\Connection; 
use App\Core\Connections\MySQL;
use App\Core\Migrations; 
use App\Core\Testing;

/* on Shell 
php -r "require 'migration.php'; up('Users');" 
php -r "require 'migration.php'; down('users');" 

INSERT INTO products (`name`)
VALUES ('Cardinal'), ('Tom B. Erichsen'), ('Skagen 21'), ('Stavanger'), ('Norway');
*/  

function up(string $table) 
{    
    $table = ucfirst(strtolower($table)); 
    // $obj = new Migrations(Connection::connect(new Mysql)); 
    // $obj->up($table);    
    $class = 'App\\Core\\Testing\\'.$table; 
    $obj = new $class();
    $obj->up($table); 
} 

function down(string $table) 
{   
    // $obj = new Migrations(Connection::connect(new Mysql)); 
    // $obj->down($table);  
    $table = ucfirst(strtolower($table));  
    $class = 'App\\Core\\Testing\\'.$table; 
    $obj = new $class();
    $obj->down($table);  
} 


?>