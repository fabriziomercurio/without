<?php
declare(strict_types=1); 

namespace App\Controllers; 
use App\Core\Connections\Connection; 
use App\Core\Connections\MySQL;
use App\Models\Product; 
use App\Core\Request; 
use App\Services\ProductService; 
use App\Core\Response;

class ProductController extends Controller 
{   
    public function __construct(private ProductService $productService = new ProductService) 
    {  
       parent::__construct(); 
    } 

    public function show() 
    {   
        $row = $this->productService->fetchAll(); 
        echo json_encode($row); 
    }

    public function edit(string $productId) 
    {   
        echo 'id of product ... ' . $productId; 
    }

    public function store(Request $request) 
    {
        $data = $this->productService->store($request); 

        if ($data === true) {
            return ['message' => 'record inserito correttamente', 'code' => 200];
        }else {
            return ['message' => 'inserimento record non riuscito', 'code' => 500];
        }
    } 

    public function delete(int $productId)
    {   
        $data = $this->productService->delete($productId); 

        if ($data === true) {
            Response::success('record delete with success');
        }else {
            Response::error('record delete failed');
        }
    }

}


?>