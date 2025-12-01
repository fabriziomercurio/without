<?php 
declare(strict_types=1); 

namespace App\Models; 
use App\Models\Model; 

class Product extends Model
{   
    public string $name, $category, $description, $brand, $code;  
    public float $price, $weight; 
    public int $available;
    
    public static function fetchAll() : array
    {   
        return self::fetchAllData('products'); 
      
    } 

    public function store(object $data) : bool
    {   
        return $this->storeData($data,'products'); 
    } 

    public static function delete(int $id) : bool
    {
       return self::deleteRecord($id,'products'); 
    }
}


?> 

