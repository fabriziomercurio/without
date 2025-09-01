<?php
declare(strict_types=1); 

namespace App\Controllers; 
use App\Core\Connections\Connection; 
use App\Core\Connections\MySQL;
use App\Models\Product; 
use App\Core\Request; 

class ProductController extends Controller 
{   
    public function show() 
    {   
        $row = Product::fetchAll();  
        $this->render->renderView('product',['name' => 'List of Products', 'rows' => $row]);  
    }

    public function edit(string $productId) 
    {   
        echo 'id of product ... ' . $productId; 
    }

    public function store(Request $request) 
    {
        $product = new Product;         
        $product->name = $request->getBody()['name']; 
        $product->surname = $request->getBody()['surname']; 
        $product->age = $request->getBody()['age']; 
        $product->city = $request->getBody()['city'];
        $data = $product->store($product); 

        if ($data === true) {
            return ['message' => 'record inserito correttamente', 'code' => 200];
        }else {
            return ['message' => 'inserimento record non riuscito', 'code' => 500];
        }
    }

}


?>