<?php
declare(strict_types=1); 

namespace App\Controllers; 
use App\Core\Connections\Connection; 
use App\Core\Connections\MySQL;

class ProductController extends Controller 
{   
    public function show() 
    {   
        $stmt = $this->pdo->prepare("SELECT * FROM products");
        $stmt->execute(); 
        $row = $stmt->fetchAll();  

        $this->render->renderView('product',['name' => 'List of Products', 'rows' => $row]);  
    }

    public function edit(string $productId) 
    {
        echo 'id of product ... ' . $productId; 
    }

}


?>