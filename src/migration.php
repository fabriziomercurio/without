<?php 

require_once "autoload.php"; 

use App\Core\Migration\Schema;

/* on Shell 
php -r "require 'migration.php'; up('Users');" 
php -r "require 'migration.php'; down('users');" 

INSERT INTO products (`name`)
VALUES ('Cardinal'), ('Tom B. Erichsen'), ('Skagen 21'), ('Stavanger'), ('Norway');
*/  

function up(string $table) 
{    
    $table = ucfirst(strtolower($table)); 
    $class = 'App\\Core\\Migration\\Schema\\'.$table;
    $obj = new $class();
    $obj->up($table); 
} 

function down(string $table) 
{   
    $table = ucfirst(strtolower($table));  
    $class = 'App\\Core\\Migration\\Schema\\'.$table; 
    $obj = new $class();
    $obj->down($table);  
} 


?>