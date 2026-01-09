<?php 
declare(strict_types=1); 

namespace App\Repositories; 
use App\Models\Product; 
use App\Core\Request; 

class ProductRepository 
{
    public function fetchAll() : array
    {
        return Product::fetchAll(); 
    } 

    public function store(Request $request)
    {
        $data = new Product; 
        $data->name = $request->getBody()['name'] ?? null; 
        $data->description = $request->getBody()['description'] ?? null; 
        $data->xMultimediaId = $request->extra['xMultimediaId'] ?? null;
        return $data->store(); 
    } 

    public function edit(int $id) : bool | array
    {
        return Product::edit($id); 
    } 

    public function update(int $id, Request $request) : bool 
    {
       return Product::update($id,$request);  
    }

    public function delete(int $id) : bool
    {
        return Product::delete($id); 
    }
}