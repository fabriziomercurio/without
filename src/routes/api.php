<?php 
declare(strict_types=1); 

use App\Controllers\LoginController;

$router->post('api/login',[LoginController::class,"doLogin"]); 



?>