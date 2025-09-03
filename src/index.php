<?php 
require_once "autoload.php"; 

use App\Core\Router; 
use App\Controllers\ProductController;  
use App\Controllers\RegisterController; 

$router = new Router; 

$router->get('about', function(){
    echo 'about run ... '; 
}); 

$router->get('login', function(){
    echo 'login run ... '; 
}); 

$router->get('register',[RegisterController::class,'show']);
$router->post('register',[RegisterController::class,'store']);

$router->get('product',[ProductController::class, 'show']);
$router->post('product',[ProductController::class, 'store']); 

$router->get('product-detail/{productId:[1-9]+}',[ProductController::class, 'edit']);

$router->get('/user/{userId:[1-9]+}/product/{product}', function(string $userId, string $product){
    echo <<<HTML
    User ID : $userId <br> Product Slug : $product
    HTML;
}); 

$router->run(); 


?>