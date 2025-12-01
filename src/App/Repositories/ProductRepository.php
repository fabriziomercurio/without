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

    public function store(Request $request) : bool 
    {
        $data = new Product; 
        $data->name = $request->getBody()['name']; 
        $data->surname = $request->getBody()['surname'];
        $data->age = $request->getBody()['age'];
        $data->city = $request->getBody()['city'];
        return $data->store($data);     
    } 

    public function delete(int $id) : bool
    {
        return Product::delete($id); 
    }
}