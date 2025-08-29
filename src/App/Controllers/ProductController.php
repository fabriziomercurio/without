<?php
declare(strict_types=1); 

namespace App\Controllers; 

class ProductController extends Controller 
{
    public function show() 
    {   
        $this->render->renderView('product',['name' => 'List of Products']);  
    }

    public function edit(string $productId) 
    {
        echo 'id of product ... ' . $productId; 
    }

}






?>