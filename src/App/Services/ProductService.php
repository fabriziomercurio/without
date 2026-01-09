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

    public function store(Request $request) 
    {
       return $this->productRepository->store($request); 
    } 

    public function edit(int $id) : bool | array
    {
       return $this->productRepository->edit($id); 
    } 

    public function update(int $id, Request $request) : bool 
    {
       return $this->productRepository->update($id,$request); 
    }

    public function delete(int $id) : bool
    {
       return $this->productRepository->delete($id); 
    }
}