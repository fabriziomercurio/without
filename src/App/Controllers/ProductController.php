<?php
declare(strict_types=1); 

namespace App\Controllers; 

class ProductController 
{
    public function show() 
    {
        echo 'show all products ... '; 
    }

    public function edit(string $productId) 
    {
        echo 'id of product ... ' . $productId; 
    }

}






?>