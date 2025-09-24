<?php 
declare(strict_types=1); 

use App\Controllers\LoginController;
use App\Controllers\RegisterController; 
use App\Controllers\ProductController; 

$router->post('api/login',[LoginController::class,"doLogin"]); 
$router->get('api/products',[ProductController::class,"show"]); 
$router->post('api/user-register',[RegisterController::class,"store"])


?>