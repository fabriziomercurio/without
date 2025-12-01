<?php 

namespace App\Services; 
use App\Repositories\ProductRepository;
use App\Core\Request;

class ProductService 
{      
    public function __construct(private ProductRepository $productRepository = new ProductRepository) 
    {} 

    public function fetchAll() : array
    {
       return $this->productRepository->fetchAll(); 
    }

    public function store(Request $request) : bool 
    {
       return $this->productRepository->store($request); 
    } 

    public function delete(int $id) : bool
    {
       return $this->productRepository->delete($id); 
    }
}