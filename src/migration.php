<?php 

require_once "autoload.php"; 

use App\Core\Connections\Connection; 
use App\Core\Connections\MySQL;
use App\Core\Migrations; 

/* on Shell 
php -r "require 'migration.php'; up('products');" 
php -r "require 'migration.php'; down('products');" 

INSERT INTO products (`name`)
VALUES ('Cardinal'), ('Tom B. Erichsen'), ('Skagen 21'), ('Stavanger'), ('Norway');
*/  

function up(string $table) 
{   
    $obj = new Migrations(Connection::connect(new Mysql)); 
    $obj->up($table);     
} 

function down(string $table) 
{   
    $obj = new Migrations(Connection::connect(new Mysql)); 
    $obj->down($table);     
} 


?>