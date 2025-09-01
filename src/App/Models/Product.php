<?php 
declare(strict_types=1); 

namespace App\Models; 
use App\Models\Model; 

class Product extends Model
{   
    public static function fetchAll() 
    {   
        return self::fetchAllData('products'); 
      
    } 

    public function store(object $data) : bool
    {   
        return $this->storeData($data,'products'); 
    }

}



?>