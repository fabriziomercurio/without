<?php
require_once "autoload.php"; 

use App\Core\Router; 
use App\Middlewares\Cors;

Cors::handle();

$router = new Router;  

foreach (glob("routes/*",GLOB_NOCHECK) as $value) {
    require_once $value ; 
}

$router->run(); 


?>