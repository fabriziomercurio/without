<?php 
require_once "autoload.php"; 

use App\Core\Router; 

$router = new Router;  

foreach (glob("routes/*",GLOB_NOCHECK) as $value) {
    require_once $value ; 
}

$router->run(); 


?>