<?php
ob_start(); 
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
require_once "autoload.php"; 

use App\Core\Router; 

$router = new Router;  

foreach (glob("routes/*",GLOB_NOCHECK) as $value) {
    require_once $value ; 
}

$router->run(); 


?>