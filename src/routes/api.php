<?php 
declare(strict_types=1); 

use App\Controllers\LoginController;
use App\Controllers\RegisterController; 
use App\Controllers\ProductController; 

$router->post('api/login',[LoginController::class,"doLogin"]); 
$router->post('api/user-register',[RegisterController::class,"store"]);

$router->get('api/products',[ProductController::class,"show"]); 
$router->put('api/product-update/{id:[0-9]+}', [ProductController::class, 'update']);
$router->get('api/product/{productId:[0-9]+}', [ProductController::class, 'edit']);
$router->delete('api/products/{productId:[0-9]+}',[ProductController::class,"delete"]);


?>