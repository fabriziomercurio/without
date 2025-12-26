<?php 
declare(strict_types=1); 

use App\Controllers\LoginController;
use App\Controllers\RegisterController; 
use App\Controllers\ProductController; 

use App\Controllers\FileUploadControllers; 

$router->get('api/test-view-upload', function(){
    echo '<form method="post" enctype="multipart/form-data">
    <input type="text" name="firstName">
    <input type="file" name="fileToUpload" id="fileToUpload">
    </br></br></br>
    <input type="submit"value="Upload Image" name="submit">
    </form>'; 
}); 

$router->post('api/test-view-upload',[FileUploadControllers::class,'store']); 

$router->post('api/login',[LoginController::class,"doLogin"]); 
$router->post('api/user-register',[RegisterController::class,"store"]);

$router->get('api/products',[ProductController::class,"show"]); 
$router->post('api/product',[ProductController::class,"store"]); 
$router->put('api/product-update/{id:[0-9]+}', [ProductController::class, 'update']);
$router->get('api/product/{productId:[0-9]+}', [ProductController::class, 'edit']);
$router->delete('api/products/{productId:[0-9]+}',[ProductController::class,"delete"]);


?>